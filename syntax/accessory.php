<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_accessory extends \dokuwiki\Extension\SyntaxPlugin
{
    public static $ACC_LINKS = array(
                            "bangle" => "gameplay/accessories#dlp_accessories",
                            "belt" => "gameplay/accessories#dlp_accessories",
                            "bracelet" => "gameplay/accessories#standard_accessories",
                            "brooch" => "gameplay/accessories#standard_accessories",
                            "choker" => "gameplay/accessories#dlp_accessories",
                            "earring" => "gameplay/accessories#standard_accessories",
                            "hairpin" => "gameplay/accessories#standard_accessories",
                            "keychain" => "gameplay/accessories#standard_accessories",
                            "necklace" => "gameplay/accessories#standard_accessories",
                            "pouch" => "gameplay/accessories#standard_accessories",
                            "ribbon" => "gameplay/accessories#standard_accessories",
                            "towel" => "gameplay/accessories#standard_accessories",
                            "wristband" => "gameplay/accessories#standard_accessories"
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
        $this->Lexer->addSpecialPattern('\{\{acc:(?:bangle:(?:a|p)|belt:(?:c|n|p)|bracelet:(?:a|e|s)|brooch:(?:c|n|s)|choker:(?:a|e|s)|earring:(?:c|p|s)|hairpin:(?:c|n|p)|keychain:(?:a|e|p)|necklace:(?:a|e|n)|pouch:(?:a|c|e)|ribbon:(?:n|p|s)|towel:(?:a|n|p)|wristband:(?:c|e|s))\}\}', $mode, 'plugin_sifas_accessory');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {    
        $type = substr($match, 6, -4);
        $attr = substr($match, -3, 1);
        return array($type . "_" . $attr, ucfirst($type) . " (" . syntax_plugin_sifas_attribute::$ATTR_NAMES[$attr] . ")", syntax_plugin_sifas_accessory::$ACC_LINKS[$type]);
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<a href='" . hsc(DOKU_BASE . $data[2]) . "' data-wiki-id='gameplay:accessories'><img class='card_thumb' title='" . hsc($data[1]) . "' alt='" . hsc($data[1]) . "' src='/sifas/wiki/images/accessory/" . hsc($data[0]) . ".png'></a>";
        return true;
    }
}

