<?php

namespace Icinga\Module\Windows\Web\Form;

use Icinga\Application\Icinga;
use Icinga\Module\Windows\Db;

abstract class WindowsForm extends QuickForm
{
    /** @var Db */
    protected $db;

    /**
     * @param Db $db
     * @return $this
     */
    public function setDb(Db $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * @return Db
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return static
     */
    public static function load()
    {
        return new static([
            'icingaModule' => Icinga::App()->getModuleManager()->getModule('windows')
        ]);
    }

    protected function addBoolean($key, $options, $default = null)
    {
        if ($default === null) {
            return $this->addElement('OptionalYesNo', $key, $options);
        } else {
            $this->addElement('YesNo', $key, $options);
            return $this->getElement($key)->setValue($default);
        }
    }

    protected function optionalBoolean($key, $label, $description)
    {
        return $this->addBoolean($key, array(
            'label'       => $label,
            'description' => $description
        ));
    }
}
