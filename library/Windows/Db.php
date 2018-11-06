<?php
/* Icinga Web 2 | (c) 2016 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Windows;

use Icinga\Application\Config;
use Icinga\Data\ConfigObject;
use Icinga\Data\Db\DbConnection;
use Icinga\Data\ResourceFactory;
use Icinga\Exception\ConfigurationError;

class Db extends DbConnection
{
    /**
     * Create and return a new instance of the WindowsDB
     *
     * @param   ConfigObject    $config     The configuration to use, otherwise the module's configuration
     *
     * @return  static
     *
     * @throws  ConfigurationError          In case no resource has been configured in the module's configuration
     */
    public static function fromConfig(ConfigObject $config = null)
    {
        if ($config === null) {
            $moduleConfig = Config::module('windows');
            if (($resourceName = $moduleConfig->get('backend', 'resource')) === null) {
                throw new ConfigurationError(
                    mt('windows', 'You need to configure a resource to access the Windows database first')
                );
            }

            return ResourceFactory::create($resourceName);
        } else {
            return ResourceFactory::createResource($config);
        }
    }
}
