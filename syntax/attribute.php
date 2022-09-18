<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_attribute extends \dokuwiki\Extension\SyntaxPlugin
{
    public static $ATTR_NAMES = array(
                            "x" => "Neutral",
                            "s" => "Smile",
                            "p" => "Pure",
                            "c" => "Cool",
                            "a" => "Active",
                            "n" => "Natural",
                            "e" => "Elegant"
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
        $this->Lexer->addSpecialPattern('\{\{attr:(?:x|s|p|c|a|n|e)\}\}', $mode, 'plugin_sifas_attribute');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $attr = substr($match, 7, -2);
        return array($attr, "Attribute: " . syntax_plugin_sifas_attribute::$ATTR_NAMES[$attr]);
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<img class='inline_icon' title='" . hsc($data[1]) . "' alt='" . hsc($data[1]) . "' src='/sifas/wiki/images/attribute/" . hsc($data[0]) . ".png'>";
        return true;
    }
}

