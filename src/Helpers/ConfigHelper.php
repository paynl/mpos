<?php

namespace MPOS\Helpers;

use Dotenv\Dotenv;
use MPOS\Enums\ErrorCodesEnum;
use MPOS\Exceptions\ConfigException;
use Symfony\Component\Yaml\Yaml;

class ConfigHelper
{
    /**
     * Config base directory must begin from DIRECTORY_SEPARATOR
     * @var string
     */
    private $configBaseDir = DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR;

    /**
     * Loaded files stack
     * @var string[]
     */
    private $files = [];

    /**
     * Config data values
     * @var mixed[]
     */
    private $configData = [];

    /**
     * Variables for replace (like %path%)
     * @var string[]
     */
    private $variables = [];

    /**
     * ConfigHelper constructor.
     *
     * @throws ConfigException
     */
    public function __construct()
    {
        $rootDir = dirname(__FILE__, 3);

        $dotenv = Dotenv::create($rootDir);
        $dotenv->load();

        $configFilesList = glob($rootDir . $this->configBaseDir . '*.yml') ?: [];
        $this->preloadVariables();
        if (count($configFilesList)) {
            foreach ($configFilesList as $file) {
                $this->loadFile($file);
            }
        } else {
            $errorCode = ErrorCodesEnum::CONFIG_NOT_FOUND;
            throw new ConfigException(ErrorCodesEnum::getErrorByCode($errorCode), $errorCode);
        }
    }

    /**
     * Load config file
     *
     * @param string $fileName
     * @throws ConfigException
     */
    private function loadFile(string $fileName) : void
    {
        $fileNameParts = explode('.', $fileName);
        $fileExtension = end($fileNameParts);
        if ($fileExtension !== 'yml') {
            $fileName .= '.yml';
        }

        // skip load loaded file
        if (in_array($fileName, $this->files)) {
            return;
        }

        $this->files[] = $fileName;

        if (is_file($fileName)) {
            $data = file_get_contents($fileName) ?: '';
        } else {
            $errorCode = ErrorCodesEnum::CONFIG_FILE_NOT_FOUND;
            throw new ConfigException(sprintf(ErrorCodesEnum::getErrorByCode($errorCode), $fileName), $errorCode);
        }

        $config = Yaml::parse($data);
        if ($config) {
            $this->configData = array_replace_recursive($this->configData, $config) ?? [];
        }
    }

    /**
     * Preload variables for replace
     */
    private function preloadVariables() : void
    {
        $rootPath = dirname(__FILE__, 3);
        $this->variables = [
            'root_path' => $rootPath,
            'src_path' => $rootPath . DIRECTORY_SEPARATOR . 'src',
            'public_path' => $rootPath . DIRECTORY_SEPARATOR . 'public',
        ];
    }

    /**
     * Get config value
     *
     * @param string $key
     * @return mixed
     * @throws ConfigException
     */
    public function get(string $key)
    {
        $search = $this->search($key);

        if ($search['found']) {
            return $search['value'];
        } else {
            $errorCode = ErrorCodesEnum::CONFIG_KEY_NOT_FOUND;
            throw new ConfigException(sprintf(ErrorCodesEnum::getErrorByCode($errorCode), $key), $errorCode);
        }
    }

    /**
     * Search config value
     *
     * @param string $key
     * @return mixed[]
     * @throws ConfigException
     */
    private function search(string $key) : array
    {
        if (empty($this->configData)) {
            $errorCode = ErrorCodesEnum::CONFIG_NOT_LOADED;
            throw new ConfigException(ErrorCodesEnum::getErrorByCode($errorCode), $errorCode);
        }

        if (!is_array($this->configData)) {
            $errorCode = ErrorCodesEnum::CONFIG_BAD_FORMAT;
            throw new ConfigException(ErrorCodesEnum::getErrorByCode($errorCode), $errorCode);
        }

        $value = false;
        $found = false;
        $configData = $this->configData;

        // if exist value in first level
        if (isset($configData[$key])) {
            $found = true;
            $value = $configData[$key];
        } else {
            // try found value in inner arrays

            // split key to array
            $keyPath = explode('.', trim($key, '.'));
            // more than one key in array?
            if (count($keyPath) > 1) {
                foreach ($keyPath as $index => $propertyKey) {
                    if (is_array($configData) && isset($configData[$propertyKey])) {
                        $configData = $configData[$propertyKey];
                        // search dotted string in current array
                        if (count($keyPath) > $index + 1) {
                            $dottedIndex = implode('.', array_slice($keyPath, $index + 1));
                            if (isset($configData[$dottedIndex])) {
                                $found = true;
                                $value = $configData[$dottedIndex];
                            }
                        }
                    }
                }
            }
        }

        if ($found) {
            $value = $this->prepareValue($key, $value);
        }

        $result = [
            'found' => $found,
            'value' => $value
        ];

        return $result;
    }

    /**
     * Prepare variables values in parameter
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws ConfigException
     */
    private function prepareValue(string $key, $value)
    {
        if (is_array($value)) {
            return $this->prepareVariables($key, $value);
        }
        if (is_string($value)) {
            return $this->prepareVariable($key, $value);
        }

        return $value;
    }

    /**
     * Prepare all config variables
     *
     * @param string $key
     * @param mixed[] $configData
     * @return mixed
     */
    private function prepareVariables(string $key, array $configData)
    {
        array_walk_recursive(
            $configData,
            function (&$value) use ($key) {
                $value = $this->prepareVariable($key, $value);
            }
        );

        return $configData;
    }

    /**
     * Prepare config variable
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws ConfigException
     */
    private function prepareVariable(string $key, $value)
    {
        $matches = null;
        preg_match('/\%(.*?)\%/', $value, $matches);
        $variable = isset($matches[1]) ? $matches[1] : false;
        if ($variable) {
            if (getenv($variable) !== false) {
                $value = str_replace("%$variable%", getenv($variable), $value);
            } elseif (isset($this->variables[$variable])) {
                $value = str_replace("%$variable%", $this->variables[$variable], $value);
            } elseif ($key != $variable) {
                // don't allow recursive search
                $search = $this->search($variable);
                if ($search['found']) {
                    $value = str_replace("%$variable%", $search['value'], $value);
                }
            }
        }

        return $value;
    }
}
