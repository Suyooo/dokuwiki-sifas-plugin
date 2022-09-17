<?php
/**
 * DokuWiki Plugin sifas (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class action_plugin_sifas_templatecard extends \dokuwiki\Extension\ActionPlugin
{
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('COMMON_PAGETPL_LOAD','BEFORE', $this, 'create_card_template');
    }

    function create_card_template(Doku_Event $event , $param) {
        global $INFO, $ID;
        if($INFO["namespace"] !== "cards") return false;
        $cardid = filter_var(substr($ID, strrpos($ID, ":") + 1), FILTER_SANITIZE_NUMBER_INT);
        if(strlen($cardid) <= 0) return;
        
        $event->data["tpl"] = action_plugin_sifas_templatecard::get_card_template($cardid);
    }
    
    static function get_card_template($cardid) {
        global $conf;
        $db = new SQLite3($conf["metadir"] . "/lookup.sqlite3");
        $mid = $db->query("SELECT member FROM members WHERE id=" . $cardid)->fetchArray();
        if (!$mid) {
          $db->close();
            return;
        }
        
        $mid = $mid["member"];
        $card = $db->query("SELECT * FROM \"".$mid."\" WHERE id=" . $cardid)->fetchArray();
        $db->close();
        
        $tpl = io_readFile(wikiFN("template:cards"));
        $tpl = str_replace("@@ATTRIBUTE@@",strtolower(helper_plugin_sifas_ids::$ATTR_IDS_R[$card["attr"]]),$tpl);
        $tpl = str_replace("@@TYPE@@",strtolower(helper_plugin_sifas_ids::$ROLE_IDS_R[$card["role"]]),$tpl);
        $tpl = str_replace("@@SKILL@@",$card["skill"],$tpl);
        $tpl = str_replace("@@PABILITY@@",$card["pability"],$tpl);
        $tpl = str_replace("@@LABILITY@@",$card["lability"],$tpl);
        $tpl = str_replace("@@NAME@@",$card["name"],$tpl);
        $tpl = str_replace("@@CARDID@@",$cardid,$tpl);
        
        $rarity = "";
        if ($card["is_fes"]) $rarity = "Fes ";
        else if ($card["is_party"]) $rarity = "Party ";
        else if ($card["rarity"] == 2) $rarity = "SR ";
        else if ($card["rarity"] == 1) $rarity = "R ";
        
        $tpl = str_replace("@@TITLE@@",helper_plugin_sifas_ids::$ATTR_IDS_R[$card["attr"]].helper_plugin_sifas_ids::$ROLE_IDS_R[$card["role"]]." ".$rarity.helper_plugin_sifas_ids::$MEMBER_IDS_R[$mid],$tpl);
        
        return $tpl;
    }
}
