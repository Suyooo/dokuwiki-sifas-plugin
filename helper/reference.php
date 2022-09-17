<?php
/**
 * DokuWiki Plugin sifas (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class helper_plugin_sifas_reference extends \dokuwiki\Extension\Plugin
{
    private $db;
                        
    function __construct() {
        global $conf;
        $this->db = new SQLite3($conf["metadir"] . "/lookup.sqlite3");
    }

    function __destruct() {
        $this->db->close();
    }

    public function getMethods() {
        $result = array();
        $result[] = array(
            'name' => 'getPattern',
            'desc' => 'Return the regex to use for the connectTo method',
            'params' => array(),
            'return' => array('regex' => 'string')
        );
        $result[] = array(
            'name' => 'getMatcher',
            'desc' => 'Return the regex to use for the handle method',
            'params' => array(),
            'return' => array('regex' => 'string')
        );
        $result[] = array(
            'name' => 'getCardIdForReference',
            'desc' => 'Given the groups matched from the matcher regex, return the card info',
            'params' => array(
                'attr' => 'string',
                'role' => 'string',
                'rarity' => 'string',
                'member' => 'string',
                'number' => 'string'
            ),
            'return' => array('card_info' => 'array')
        );
        $result[] = array(
            'name' => 'getCanonicalNameForCardId',
            'desc' => 'Given the card ID, return the link name',
            'params' => array(
                'cid' => 'integer'
            ),
            'return' => array('canonical_name' => 'string')
        );
        $result[] = array(
            'name' => 'getPageNameForCardId',
            'desc' => 'Given the card ID, return the respective wiki page name',
            'params' => array(
                'cid' => 'integer'
            ),
            'return' => array('page_name' => 'string')
        );
        return $result;
    }

    public function getPattern()
    {
        return '(?:S|P|C|A|N|E)(?:Vo|Gd|Sp|Sk)(?: Fes| Party| SR| R)? (?:Honoka|Eli|Kotori|Umi|Rin|Maki|Nozomi|Hanayo|Nico|Chika|Riko|Kanan|Dia|You|Yoshiko|Hanamaru|Mari|Ruby|Ayumu|Kasumi|Shizuku|Karin|Ai|Kanata|Setsuna|Emma|Rina|Shioriko|Mia|Lanzhu)(?: \d+)?';
    }
    
    public function getMatcher()
    {
        return '(S|P|C|A|N|E)(Vo|Gd|Sp|Sk)( Fes| Party| SR| R)? (Honoka|Eli|Kotori|Umi|Rin|Maki|Nozomi|Hanayo|Nico|Chika|Riko|Kanan|Dia|You|Yoshiko|Hanamaru|Mari|Ruby|Ayumu|Kasumi|Shizuku|Karin|Ai|Kanata|Setsuna|Emma|Rina|Shioriko|Mia|Lanzhu)( \d+)?';
    }
    
    public function getCardIdForReference($attr, $role, $rarity, $member, $number)
    {
        $mid = helper_plugin_sifas_ids::$MEMBER_IDS[$member];
        $aid = helper_plugin_sifas_ids::$ATTR_IDS[$attr];
        $rid = helper_plugin_sifas_ids::$ROLE_IDS[$role];
        $skip = !$number ? 0 : intval(substr($number,1))-1;
        
        $r = "3";
        if ($rarity === " Fes") $r = "3 AND is_fes = 1";
        else if ($rarity === " Party") $r = "3 AND is_party = 1";
        else if ($rarity === " SR") $r = "2";
        else if ($rarity === " R") $r = "1";
        
        $q = "SELECT id, rarity, is_party, is_fes FROM \"" . $mid . "\" WHERE rarity=" . $r . " AND attr=" . $aid . " AND role=" . $rid;
        $res = $this->db->query($q);
        while ($skip-- > 0 && $row = $res->fetchArray()) {
        }
        
        if ($row = $res->fetchArray()) {
            $cid = $row["id"];
            $rarityname = $row["is_fes"] == 1 ? " Fes" : ($row["is_party"] == 1 ? " Party" : ($row["rarity"] == 2 ? " SR" : ($row["rarity"] == 1 ? " R" : "")));
            $match = $attr.$role.$rarityname." ".$member;
        }
        
        if ($cid) {
            return array(
                'card_id' => $cid,
                'canonical_name' => $match
            );
        } else {
            return NULL;
        }
    }
    
    public function getCanonicalNameForCardId($cid)
    {
        $q = "SELECT member FROM members WHERE id=" . $cid;
        $res = $this->db->query($q);
        if ($row = $res->fetchArray()) {
            $mid = $row["member"];
        } else {
            return NULL;
        }
        
        $q = "SELECT attr, role, rarity, is_party, is_fes FROM \"" . $mid . "\" WHERE id=" . $cid;
        $res = $this->db->query($q);
        
        if ($row = $res->fetchArray()) {
            $rarityname = $row["is_fes"] == 1 ? " Fes" : ($row["is_party"] == 1 ? " Party" : ($row["rarity"] == 2 ? " SR" : ($row["rarity"] == 1 ? " R" : "")));
            $member = helper_plugin_sifas_ids::$MEMBER_IDS_R[$mid];
            $attr = helper_plugin_sifas_ids::$ATTR_IDS_R[$row["attr"]];
            $role = helper_plugin_sifas_ids::$ROLE_IDS_R[$row["role"]];
            return $attr.$role.$rarityname." ".$member;
        } else {
            return NULL;
        }
    }
    
    public function getPageNameForCardId($cid)
    {
        return $this->db->query("SELECT page FROM wikipages WHERE id=" . $cid)->fetchArray()["page"];
    }
}

