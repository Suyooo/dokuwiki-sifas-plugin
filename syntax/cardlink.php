<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_cardlink extends \dokuwiki\Extension\SyntaxPlugin
{
    public function getType()
    {
        return 'substition';
    }

    public function getSort()
    {
        return 287;
    }

    /** @inheritDoc */
    public function connectTo($mode)
    {
        $ref = $this->loadHelper('sifas_reference');
        $this->Lexer->addSpecialPattern('\b' . $ref->getPattern() . '\b', $mode, 'plugin_sifas_cardlink');
        $this->Lexer->addSpecialPattern('\[\[cards:(?:\d+)\]\]', $mode, 'plugin_sifas_cardlink');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        global $conf;
        $ref = $this->loadHelper('sifas_reference');
        if (preg_match('/\[\[cards:(\d+)\]\]/', $match, $matches, PREG_UNMATCHED_AS_NULL)) {
            $cid = (int)$matches[1];
            $data = array(
                'card_id' => $cid,
                'canonical_name' => $ref->getCanonicalNameForCardId($cid)
            );
        } else {
            preg_match('/\b' . $ref->getMatcher() . '\b/', $match, $matches, PREG_UNMATCHED_AS_NULL);
            $data = $ref->getCardIdForReference($matches[1],$matches[2],$matches[3],$matches[4],$matches[5]);
        }
        
        if ($data != NULL) {
            $data['page_name'] = $ref->getPageNameForCardId($data['card_id']);
        } else {
            $data = array('failed_match' => $match);
        }
        return $data;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode == 'xhtml') {
            if ($data['card_id']) {
                $renderer->doc .= "<a class='cardlink wikilink" . (page_exists("cards:" . $data['page_name']) ? 1 : "2' rel='nofollow") . "' href='" . DOKU_BASE . "cards/" . $data['page_name'] . "' data-wiki-id='cards:" . $data['page_name'] . "'><img src='/sifas/wiki/images/card_thumb_idlz/" . $data['card_id'] . ".png'> " . $data['canonical_name'] . "</a>";
            } else {
                $renderer->doc .= $data['failed_match'];
            }
            return true;
        }
        if($mode == 'metadata') {
            $renderer->internallink("cards:".$data[1]);
            return true;
        }
        return false;
    }
}

