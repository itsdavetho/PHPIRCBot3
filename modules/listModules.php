<?php
    class listModules
    {
        public function execute(IRCBot $irc_bot)
        {
            $module_list = '';
            $modules = $irc_bot->getModules();
            asort($modules);
            foreach ($modules as $module) {
                $methods = get_class_methods($module);
                $module_list .= get_class($module) . ', ';
            }
            echo 'Modules: ' . substr($module_list, 0, -2);
        }
    }
