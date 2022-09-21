<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_skill extends \dokuwiki\Extension\SyntaxPlugin
{
    public static $SKILL_NAMES = array(
                            "appeal" => "Appeal+",
                            "cleanse" => "Cleanse Debuffs",
                            "critchance" => "Critical Chance Up",
                            "critpower" => "Crit Power Up",
                            "dr" => "Damage Reduction",
                            "heal" => "Heal",
                            "p:appeal" => "Appeal+ (Passive)",
                            "p:stamina" => "Stamina+ (Passive)",
                            "p:technique" => "Technique+ (Passive)",
                            "p:typebonus" => "Type Bonus+ (Passive)",
                            "shield" => "Shield",
                            "skillchance" => "Skill Chance Up",
                            "spfill" => "SP Gauge Fill",
                            "spgain" => "SP Gauge Gain Up",
                            "spocpower" => "SP Overcharge & SP Power Up",
                            "sppower" => "SP Power Up",
                            "uncapcrit" => "Increase Crit Voltage Cap",
                            "vogain" => "Voltage Gain Up",
                            "voplus" => "Voltage+"
                        );
                        
    public function getType()
    {
        return 'substition';
    }
    
    public function getSort()
    {
        return 285;
    }

    /** @inheritDoc */
    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('\{\{skill:(?:'.implode("|",array_keys(syntax_plugin_sifas_skill::$SKILL_NAMES)).')\}\}', $mode, 'plugin_sifas_skill');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $skill = substr($match, 8, -2);
        $img = str_replace(":", "_", $skill);
        return array($img, "Skill: " . syntax_plugin_sifas_skill::$SKILL_NAMES[$skill]);
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<img class='inline_icon' title='" . hsc($data[1]) . "' alt='" . hsc($data[1]) . "' src='/sifas/wiki/images/skill/" . hsc($data[0]) . ".png'>";
        return true;
    }
}

