<?php
/**
 * Desktop start menu builder
 *
 * @package appFlower
 * @author Sergey Startsev <startsev.sergey@gmail.com>
 */
class afExtjsDesktopStartMenuBuilder
{
    /**
     * Menu identificator in definition
     */
    const MENU_IDENTIFICATOR = 'menu';
    
    /**
     * Item type identificator in item attributes
     */
    const ITEM_TYPE = 'type';
    
    /**
     * Main identificator
     */
    const MAIN = 'main';
    
    /**
     * Tools identificator
     */
    const TOOLS = 'tools';
    
    /**
     * Menu definition
     *
     * @var array
     */
    protected $definition;
    
    /**
     * Menu instance
     *
     * @var afExtjsStartMenu
     */
    protected $menu_instance;
    
    /**
     * Private constructor
     */
    private function __construct() {}
    
    /**
     * Fabric method creator
     *
     * @param string $place 
     * @param string $place_type 
     * @return afExtjsDesktopStartMenuBuilder
     * @author Sergey Startsev
     */
    static public function create($place = 'frontend', $place_type = 'app')
    {
        $instance = new self;
        
        $path = self::getHelperPath($place, $place_type);
        
        if (!file_exists($path)) throw new afExtjsDesktopStartMenuBuilderException("Helper file '{$path}' doesn't exists");
        
        $instance->definition = afExtjsBuilderParser::create($path)->parse()->get(self::MENU_IDENTIFICATOR);
        $instance->menu_instance = new afExtjsStartMenu(afExtjsBuilderParser::getAttributes($instance->definition));
        
        return $instance;
    }
    
    /**
     * Getting helper file path
     *
     * @param string $place 
     * @param string $place_type 
     * @return string
     * @author Sergey Startsev
     */
    static public function getHelperPath($place = 'frontend', $place_type = 'app')
    {
        return sfConfig::get("sf_{$place_type}s_dir") . "/{$place}/config/" . afExtjsBuilderParser::HELPER_FILE;
    }
    
    /**
     * Getting main from definition
     *
     * @param Array $def 
     * @return array
     * @author Sergey Startsev
     */
    static public function getMain(Array $def)
    {
        return (array_key_exists(self::MAIN, $def)) ? $def[self::MAIN] : array();
    }
    
    /**
     * Getting tools from definition
     *
     * @param Array $def 
     * @return array
     * @author Sergey Startsev
     */
    static public function getTools(Array $def)
    {
        return (array_key_exists(self::TOOLS, $def)) ? $def[self::TOOLS] : array();
    }
    
    /**
     * Processing - building menu
     *
     * @return afExtjsDesktopStartMenuBuilder
     * @author Sergey Startsev
     */
    public function process()
    {
        $this->processTools();
        $this->processItems($this->menu_instance, self::getMain($this->definition));
        
        return $this;
    }
    
    /**
     * Settign menu instance
     *
     * @param afExtjsStartMenu $menu 
     * @return void
     * @author Sergey Startsev
     */
    public function setMenuInstance(afExtjsStartMenu $menu)
    {
        $this->menu_instance = $menu;
    }
    
    /**
     * Getting menu instance
     *
     * @return afExtjsStartMenu
     * @author Sergey Startsev
     */
    public function getMenuInstance()
    {
        return $this->menu_instance;
    }
    
    /**
     * Process tool area
     *
     * @return void
     * @author Sergey Startsev
     */
    private function processTools()
    {
        foreach (self::getTools($this->definition) as $tool) {
            $this->getMenuInstance()->addTool($tool);
        }
    }
    
    /**
     * Process with items
     *
     * @param afExtjsStartMenu $glue_instance 
     * @param Array $definition 
     * @return void
     * @author Sergey Startsev
     */
    private function processItems(afExtjsStartMenu $glue_instance, Array $definition)
    {
        foreach ($definition as $item_name => $item) {
            $this->getItemInstance($glue_instance, $item);
        }
    }
    
    /**
     * Process with single item
     *
     * @param afExtjsStartMenu $glue_instance 
     * @param Array $definition 
     * @return void
     * @author Sergey Startsev
     */
    private function getItemInstance(afExtjsStartMenu $glue_instance, Array $definition)
    {
        $attributes = afExtjsBuilderParser::getAttributes($definition);
        $children = afExtjsBuilderParser::getChildren($definition);
        
        $type = 'item';
        if (array_key_exists(self::ITEM_TYPE, $attributes)) $type = $attributes[self::ITEM_TYPE];
        $type = ucfirst($type);
        
        $reflection = new ReflectionClass("afExtjsStartMenu{$type}");
        
        $instance = $reflection->newInstance($glue_instance, $attributes);
        
        if (!empty($children)) {
            $menu_reflection = new ReflectionClass("afExtjsStartMenu");
            $menu_instance = $menu_reflection->newInstance($instance);
            $this->processItems($menu_instance, $children);
            $menu_instance->end();
        }
        
        $instance->end();
    }
    
}
