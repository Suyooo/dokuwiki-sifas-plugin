<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_group extends \dokuwiki\Extension\SyntaxPlugin
{
    public static $GROUP_NAMES = array(
                            "m" => "Group: µ&apos;s",
                            "a" => "Group: Aqours",
                            "n" => "Group: Nijigaku",
                            "1" => "Group: 1st Years",
                            "2" => "Group: 2nd Years",
                            "3" => "Group: 3rd Years",
                            "f" => "Position: Frontline",
                            "b" => "Position: Backline"
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
        $this->Lexer->addSpecialPattern('\{\{group:(?:m|a|n|1|2|3|f|b)\}\}', $mode, 'plugin_sifas_group');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $group = substr($match, 8, -2);
        return array($group, syntax_plugin_sifas_group::$GROUP_NAMES[$group]);
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }
        $renderer->doc .= "<img class='inline_icon' title='" . hsc($data[1]) . "' alt='" . hsc($data[1]) . "' src='/sifas/wiki/images/group/" . hsc($data[0]) . ".png'>";
        return true;
    }
}

