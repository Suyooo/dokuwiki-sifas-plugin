<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_cardwithref extends \dokuwiki\Extension\SyntaxPlugin
{
    public function getType()
    {
        return 'substition';
    }

    public function getSort()
    {
        return 286;
    }

    /** @inheritDoc */
    public function connectTo($mode)
    {
        $ref = $this->loadHelper('sifas_reference');
        $this->Lexer->addSpecialPattern('\{\{card:(?:full:)?(?:unidolized:)?' . $ref->getPattern() . '\}\}', $mode, 'plugin_sifas_cardwithref');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        global $conf;
        $ref = $this->loadHelper('sifas_reference');
        preg_match('/\{\{card:(full:)?(unidolized:)?' . $ref->getMatcher() . '\}\}/', $match, $matches, PREG_UNMATCHED_AS_NULL);
        
        $data = $ref->getCardIdForReference($matches[3],$matches[4],$matches[5],$matches[6],$matches[7]);
        
        if ($data == NULL) {
            return NULL;
        }
        
        return array($matches[1] !== NULL, $matches[2] !== NULL, $data['card_id'], $ref->getPageNameForCardId($data['card_id']));
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($data !== NULL) {
            if ($mode == 'xhtml') {
                $renderer->doc .= "<a href='" . hsc(DOKU_BASE . "cards/" . $data[3]) . "'" . (page_exists("cards:" . $data[3]) ? "" : " rel='nofollow'") . " data-wiki-id='cards:" . hsc($data[3]) . "'><img class='card_" . ($data[0] ? "full" : "thumb") . "' src='/sifas/wiki/images/card_" . ($data[0] ? "full" : "thumb") . ($data[1] ? "" : "_idlz") . "/" . hsc($data[2]) . ".png'></a>";
                return true;
            }
            if($mode == 'metadata') {
                $renderer->internallink("cards:".$data[3]);
                return true;
            }
        }
        return false;
    }
}

