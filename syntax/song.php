<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_song extends \dokuwiki\Extension\SyntaxPlugin
{
    public function getType()
    {
        return 'substition';
    }

    public function getSort()
    {
        return 285;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('\{\{song:(?:\d*?)\}\}', $mode, 'plugin_sifas_song');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        preg_match('/\{\{song:(\d*?)\}\}/', $match, $matches, PREG_UNMATCHED_AS_NULL);
        return $matches[1];
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<img class='song_jacket' src='/sifas/wiki/images/song_jacket/" . hsc($data) . ".png'>";
        return true;
    }
}

