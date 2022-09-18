<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_type extends \dokuwiki\Extension\SyntaxPlugin
{
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
        $this->Lexer->addSpecialPattern('\{\{type:(?:vo|sp|gd|sk)\}\}', $mode, 'plugin_sifas_type');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $type = substr($match, 7, -2);
        return array($type, "Type: " . ucfirst($type));
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<img class='inline_icon' title='" . hsc($data[1]) . "' alt='" . hsc($data[1]) . "' src='/sifas/wiki/images/type/" . hsc($data[0]) . ".png'>";
        return true;
    }
}

