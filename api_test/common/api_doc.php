<?php

class apiDocument {
    public $name;
    public $description;
    public $url;
    public $parameters;
    public $return;
}

class apiParameter {
    public $name;
    public $description;
    public $sample_value;
    public $required;

    public function __construct($name, $description, $sample_value = null, $required = false)
    {
        $this->name = $name;
        $this->description = $description;
        $this->sample_value = $sample_value;
        $this->required = $required;
    }
}