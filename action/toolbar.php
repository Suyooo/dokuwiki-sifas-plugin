<?php
/**
 * DokuWiki Plugin sifas (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class action_plugin_sifas_toolbar extends \dokuwiki\Extension\ActionPlugin
{

    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button', array());
    }

    function insert_button(Doku_Event $event, $param) {
        // GENERAL
    
        for ($i=0; $i<count($event->data); $i++) { 
            error_log($event->data[$i]['title']);
            if($event->data[$i]['title'] == 'Smileys') {
                unset($event->data[$i]);        
            }
        }
    
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Table",
            'icon' => 'table.png',
            'list' => array(
                "^ A1 ^ B1 ^\n^ A2 | B2 |\n^ A3 | B3 |",
                "^ A1 ^ B1 ^ C1 ^\n^ A2 | B2 | C2 |\n^ A3 | B3 | C3 |",
                "^ A1 ^ B1 ^ C1 ^ D1 ^\n^ A2 | B2 | C2 | D2 |\n^ A3 | B3 | C3 | D3 |"
            )
        );
    
        $event->data[] = array (
            'type' => 'format',
            'title' => "Math (TeX)",
            'icon' => 'math.png',
            'open' => "$$",
            'close' => "$$",
            'sample' => "\\frac{1}{3}"
        );
        
        // SIFAS
        
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Card Icons",
            'icon' => '../../../images/card_thumb_idlz/4.png',
            'icobase' => '../../images',
            'list' => array(
                "{{card:Card No. or Reference}}" => 'card_thumb_idlz/4.png',
                "{{card:unidolized:Card No. or Reference}}" => 'card_thumb/4.png'
            )
        );
        
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Attribute Icons",
            'icon' => '../../../images/attribute/s.png',
            'icobase' => '../../images/attribute',
            'list' => array(
                "{{attr:s}}" => 's.png',
                "{{attr:p}}" => 'p.png',
                "{{attr:c}}" => 'c.png',
                "{{attr:a}}" => 'a.png',
                "{{attr:n}}" => 'n.png',
                "{{attr:e}}" => 'e.png',
                "{{attr:x}}" => 'x.png'
            )
        );
        
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Type Icons",
            'icon' => '../../../images/type/vo.png',
            'icobase' => '../../images/type',
            'list' => array(
                "{{type:vo}}" => 'vo.png',
                "{{type:sp}}" => 'sp.png',
                "{{type:gd}}" => 'gd.png',
                "{{type:sk}}" => 'sk.png'
            )
        );
        
        $groups = [];
        foreach (syntax_plugin_sifas_group::$GROUP_NAMES as $group => $name) {
            $groups['{{group:'.$group.'}}'] = $group . ".png";
        }
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Group Icons",
            'icon' => '../../../images/group/f.png',
            'icobase' => '../../images/group',
            'list' => $groups
        );
        
        $skills = [];
        foreach (syntax_plugin_sifas_skill::$SKILL_NAMES as $skill => $name) {
            $skills['{{skill:'.$skill.'}}'] = str_replace(":", "_", $skill) . ".png";
        }
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Skill Icons",
            'icon' => '../../../images/skill/voplus.png',
            'icobase' => '../../images/skill',
            'list' => $skills
        );
        
        $accs = [];
        foreach (syntax_plugin_sifas_accessory::$ACC_ATTRS as $type => $attrs) {
            foreach ($attrs as $attr) {
                $accs['{{acc:'.$type.':'.$attr.'}}'] = $type . "_" . $attr . ".png";
            }
        }
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Accessory Icons",
            'icon' => '../../../images/accessory/brooch_s.png',
            'icobase' => '../../images/accessory',
            'list' => $accs
        );
    
        $event->data[] = array (
            'type' => 'picker',
            'title' => "Limit Break Icons",
            'icon' => '../../../images/limit_break/1.png',
            'list' => array(
                "{{lb:0}}", "{{lb:1}}", "{{lb:2}}", "{{lb:3}}", "{{lb:4}}", "{{lb:5}}"
            )
        );
    }
}
