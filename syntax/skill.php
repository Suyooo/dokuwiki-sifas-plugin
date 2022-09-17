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
                            "critchance" => "Critical Chance Up",
                            "critpower" => "Crit Power Up",
                            "dr" => "Damage Reduction",
                            "heal" => "Heal",
                            "p_appeal" => "Appeal+ (Passive)",
                            "p_stamina" => "Stamina+ (Passive)",
                            "p_technique" => "Technique+ (Passive)",
                            "p_typebonus" => "Type Bonus+ (Passive)",
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
        $this->Lexer->addSpecialPattern('\{\{skill:(?:appeal|critchance|critpower|dr|heal|shield|skillchance|spfill|spgain|spocpower|sppower|uncapcrit|vogain|voplus|p:appeal|p:stamina|p:technique|p:typebonus)\}\}', $mode, 'plugin_sifas_skill');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $skill = str_replace(":", "_", substr($match, 8, -2));
        return array($skill, "Skill: " . syntax_plugin_sifas_skill::$SKILL_NAMES[$skill]);
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<img class='inline_icon' title='" . $data[1] . "' alt='" . $data[1] . "' src='/sifas/wiki/images/skill/" . $data[0] . ".png'>";
        return true;
    }
}

