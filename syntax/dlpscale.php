<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_dlpscale extends \dokuwiki\Extension\SyntaxPlugin
{
    public static $SCALE_NAMES = array(
                            -2 => "No need",
                            -1 => "Save cards",
                            0 => "Note",
                            1 => "Useful",
                            2 => "Important"
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
        $this->Lexer->addSpecialPattern('\{\{dlpscale:(?:[-+][12]|0)\}\}', $mode, 'plugin_sifas_dlpscale');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $value = (int) substr($match, 11, -2);
        return array($value, syntax_plugin_sifas_dlpscale::$SCALE_NAMES[$value]);
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<img class='inline_icon dlpscale' title='" . hsc($data[1]) . "' alt='" . hsc($data[1]) . "' src='/sifas/wiki/images/dlpscale/" . hsc($data[0]) . ".png'>";
        return true;
    }
}

