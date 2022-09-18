<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_lb extends \dokuwiki\Extension\SyntaxPlugin
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
        $this->Lexer->addSpecialPattern('\{\{lb:(?:0|1|2|3|4|5)\}\}', $mode, 'plugin_sifas_lb');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {    
        $lb = (int) substr($match, 5, -2);
        $title = "Limit Break " . $lb;
        if ($lb === 5) $title = "Max Limit Break";
        return array($lb, $title);
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<div title='" . hsc($data[1]) . "' class='limitbreak'>";
        $renderer->doc .= "<img src='/sifas/wiki/images/limit_break/" . ($data[0] >= 1 ? 1 : 0) . ".png'>";
        $renderer->doc .= "<img src='/sifas/wiki/images/limit_break/" . ($data[0] >= 2 ? 1 : 0) . ".png'>";
        $renderer->doc .= "<img src='/sifas/wiki/images/limit_break/" . ($data[0] >= 3 ? 1 : 0) . ".png'>";
        $renderer->doc .= "<img src='/sifas/wiki/images/limit_break/" . ($data[0] >= 4 ? 1 : 0) . ".png'>";
        $renderer->doc .= "<img src='/sifas/wiki/images/limit_break/" . ($data[0] >= 5 ? 1 : 0) . ".png'>";
        $renderer->doc .= "</div>";
        return true;
    }
}

