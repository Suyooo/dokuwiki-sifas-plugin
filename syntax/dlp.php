<?php
/**
 * DokuWiki Plugin sifas (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Suyooo <me@suyo.be>
 */
class syntax_plugin_sifas_dlp extends \dokuwiki\Extension\SyntaxPlugin
{
    public function getType()
    {
        return 'formatting';
    }

    public function getAllowedTypes() {
        return array();
    }   
    
    public function getSort() {
        return 5;
    }

    /** @inheritDoc */
    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('<DLP_PARADE>(?=.*?</DLP_PARADE>)',$mode,'plugin_sifas_dlp');
    }
    public function postConnect() {
        $this->Lexer->addExitPattern('</DLP_PARADE>','plugin_sifas_dlp');
    }
    
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        if ($state !== DOKU_LEXER_UNMATCHED) {
            return NULL;
        }
        $table = '<div class="table sectionedit1"><table class="inline" style="min-width: 0px; width: 100%;"><colgroup><col style="width: 40%"><col style="width: 10%"><col style="width: 10%"><col style="width: 10%"><col style="width: 10%"><col style="width: 10%"><col style="width: 10%"></colgroup><thead><tr class="row0"><th class="col0">Overview Table</th><th class="col1 rightalign" colspan="6"><b><img class="inline_icon dlpscale" alt="Important" src="/sifas/wiki/images/dlpscale/2.png"> Important <img class="inline_icon dlpscale" alt="Useful" src="/sifas/wiki/images/dlpscale/1.png"> Useful <img class="inline_icon dlpscale" alt="Save cards" src="/sifas/wiki/images/dlpscale/-1.png"> Save cards <img class="inline_icon dlpscale" alt="No need" src="/sifas/wiki/images/dlpscale/-2.png"> No need</b><br>Hover cursor over icons or click the stage name for hint details</th></tr><tr class="row1"><th class="col0"></th><th class="col1 centeralign">Attribute</th><th class="col2 centeralign">Type</th><th class="col3 centeralign">Group</th><th class="col4 centeralign">Roles</th><th class="col5 centeralign">Backline</th><th class="col6 centeralign">Other</th></tr></thead><tbody>';
        
        $parts = explode("\n===== ", $match);
        $newparts = array(array_shift($parts));
        if (count($parts) == 0) {
            $parts = array($match);
        }
        $first = true;
        $i = 2;
        $allanchors = array();
        
        error_log(print_r($parts,true));
        foreach ($parts as $part) {
            if ($first) $first = false;
            else {
                $table .= "<tr class='row".$i."'><td class='col0' colspan='7'></td></tr>";
                $i++;
            }
            
            $stages = explode("\n==== ", $part);
            $newstages = array(array_shift($stages));
            foreach ($stages as $stage) {
                $attr = substr($stage, strpos($stage, "{{attr:") + 7, 1);
                $name = substr($stage, 0, strpos($stage, " ===="));
                $anchorbase = preg_replace("/[ ,]+/", "_", trim(preg_replace('/^[0-9]+|[:!?.()"+]/', '', strtolower($name))));
                $anchor = $anchorbase;
                $anchorno = 1;
                while (array_key_exists($anchor,$allanchors)) {
                    $anchor = $anchorbase . $anchorno;
                    $anchorno++;
                }
                $allanchors[$anchor] = true;
                
                $table .= "<tr class='row".$i."'><th class='col0'><a href='#".$anchor."' class='wikilink1'><img class='inline_icon' title='Attribute: " . hsc(syntax_plugin_sifas_attribute::$ATTR_NAMES[$attr]) . "' alt='Attribute: " . hsc(syntax_plugin_sifas_attribute::$ATTR_NAMES[$attr]) . "' src='/sifas/wiki/images/attribute/" . hsc($attr) . ".png'> ".hsc($name)."</a></th><td class='col1 centeralign'>";
                $i++;
                
                $lines = explode("\n", $stage);
                $newlines = array();
                $allhtmlicons = array();
                $putsizeline = false;
                foreach ($lines as $line) {
                    if (!$putsizeline && strlen($line)>0 && $line[0] == "^") {
                        $putsizeline = true;
                        $newlines[] = "|< 100% 1% 15% 18% 15% 18% 15% 18% >|";
                    }
                    $wikiicons = array();
                    $htmlicons = array();

                    preg_match_all('/\{\{dlp:attr:(s|p|c|a|n|e|x):([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    $hint = substr($line, strpos($line,"|") + 1);
                    $hint = trim(substr($hint, 0, strpos($hint," |")));
                    foreach ($matches_list as $matches) {
                        $wikiicons["Attribute"][] = "{{attr:".$matches[1]."}}{{dlpscale:" . $matches[2] . "}}";
                        $htmlicons["Attribute"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Attribute: " . hsc(syntax_plugin_sifas_attribute::$ATTR_NAMES[$matches[1]]) . "' src='/sifas/wiki/images/attribute/" . hsc($matches[1]) . ".png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[2]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[2]) . ".png'></span>";
                    }
                    preg_match_all('/\{\{dlp:type:(vo|sp|gd|sk):([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Type"][] = "{{type:".$matches[1]."}}{{dlpscale:" . $matches[2] . "}}";
                        $htmlicons["Type"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Type: " . hsc(ucfirst($matches[1])) . "' src='/sifas/wiki/images/type/" . hsc($matches[1]) . ".png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[2]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[2]) . ".png'></span>";
                    }
                    preg_match_all('/\{\{dlp:group:(1|2|3|m|a|n):([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Group"][] = "{{group:".$matches[1]."}}{{dlpscale:" . $matches[2] . "}}";
                        $htmlicons["Group"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Group: " . hsc(syntax_plugin_sifas_group::$GROUP_NAMES[$matches[1]]) . "' src='/sifas/wiki/images/group/" . hsc($matches[1]) . ".png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[2]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[2]) . ".png'></span>";
                    }
                    
                    preg_match_all('/\{\{dlp:backline:attr:(s|p|c|a|n|e|x):([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Backline"][] = "{{attr:".$matches[1]."}}{{dlpscale:" . $matches[2] . "}}";
                        $htmlicons["Backline"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Backline: " . hsc(syntax_plugin_sifas_attribute::$ATTR_NAMES[$matches[1]]) . "' src='/sifas/wiki/images/attribute/" . hsc($matches[1]) . ".png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[2]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[2]) . ".png'></span>";
                    }
                    preg_match_all('/\{\{dlp:backline:type:(vo|sp|gd|sk):([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Backline"][] = "{{type:".$matches[1]."}}{{dlpscale:" . $matches[2] . "}}";
                        $htmlicons["Backline"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Backline: " . hsc(ucfirst($matches[1])) . "' src='/sifas/wiki/images/type/" . hsc($matches[1]) . ".png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[2]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[2]) . ".png'></span>";
                    }
                    preg_match_all('/\{\{dlp:backline:group:(1|2|3|m|a|n):([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Backline"][] = "{{group:".$matches[1]."}}{{dlpscale:" . $matches[2] . "}}";
                        $htmlicons["Backline"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Backline: " . hsc(syntax_plugin_sifas_group::$GROUP_NAMES[$matches[1]]) . "' src='/sifas/wiki/images/group/" . hsc($matches[1]) . ".png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[2]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[2]) . ".png'></span>";
                    }
                    
                    preg_match_all('/\{\{dlp:scoring:([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Scoring"][] = "{{skill:voplus}}{{dlpscale:" . $matches[1] . "}}";
                        $htmlicons["Roles"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='High Scoring' src='/sifas/wiki/images/skill/voplus.png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[1]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[1]) . ".png'></span>";
                    }
                    preg_match_all('/\{\{dlp:healing:([-+][12]|0)\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Sustain"][] = "{{skill:heal}}{{dlpscale:" . $matches[1] . "}}";
                        $htmlicons["Roles"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Sustain' src='/sifas/wiki/images/skill/heal.png'><img class='inline_icon dlpscale' alt='" . hsc(syntax_plugin_sifas_dlpscale::$SCALE_NAMES[(int) $matches[1]]) . "' src='/sifas/wiki/images/dlpscale/" . ((int) $matches[1]) . ".png'></span>";
                    }
                    
                    preg_match_all('/\{\{dlp:other:cleanse\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Cleansable"][] = "{{skill:cleanse}}";
                        $htmlicons["Other"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Cleansable' src='/sifas/wiki/images/skill/cleanse.png'></span>";
                    }
                    preg_match_all('/\{\{dlp:other:swap\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Swaps required"][] = "{{group:b}}";
                        $htmlicons["Other"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Backline' src='/sifas/wiki/images/group/b.png'></span>";
                    }
                    preg_match_all('/\{\{dlp:other:sp\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["SP AC"][] = "{{skill:spgain}}";
                        $htmlicons["Other"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='SP AC' src='/sifas/wiki/images/skill/spgain.png'></span>";
                    }
                    preg_match_all('/\{\{dlp:other:crit\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Crit AC"][] = "{{skill:critchance}}";
                        $htmlicons["Other"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Crit AC' src='/sifas/wiki/images/skill/critchance.png'></span>";
                    }
                    preg_match_all('/\{\{dlp:other:skill\}\}/', $line, $matches_list, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);
                    foreach ($matches_list as $matches) {
                        $wikiicons["Skill AC"][] = "{{skill:skillchance}}";
                        $htmlicons["Other"][] = "<span title='".hsc($hint)."'><img class='inline_icon' alt='Crit AC' src='/sifas/wiki/images/skill/skillchance.png'></span>";
                    }
                    
                    if (count($wikiicons) == 0) $newlines[] = $line;
                    else $newlines[] = "^  " . implode("", array_unique(array_merge(...array_values($wikiicons)))) . "  ^ " . implode(" / ", array_keys($wikiicons)) . " | " . $hint . " |||||";
                    $allhtmlicons = array_merge_recursive($allhtmlicons,$htmlicons);
                }
                
                error_log(print_r($allhtmlicons,true));
                if (array_key_exists("Attribute",$allhtmlicons)) $table .= implode("", array_unique($allhtmlicons["Attribute"]));
                $table .= '</td><td class="col2 centeralign">';
                if (array_key_exists("Type",$allhtmlicons)) $table .= implode("", array_unique($allhtmlicons["Type"]));
                $table .= '</td><td class="col3 centeralign">';
                if (array_key_exists("Group",$allhtmlicons)) $table .= implode("", array_unique($allhtmlicons["Group"]));
                $table .= '</td><td class="col4 centeralign">';
                if (array_key_exists("Roles",$allhtmlicons)) $table .= implode("", array_unique($allhtmlicons["Roles"]));
                $table .= '</td><td class="col5 centeralign">';
                if (array_key_exists("Backline",$allhtmlicons)) $table .= implode("", array_unique($allhtmlicons["Backline"]));
                $table .= '</td><td class="col6 centeralign">';
                if (array_key_exists("Other",$allhtmlicons)) $table .= implode("", array_unique($allhtmlicons["Other"]));
                $table .= '</td></tr>';
                
                $newstages[] = implode("\n", $newlines);
            }
            $newparts[] = implode("\n==== ", $newstages);
        }
        //error_log($table);
        return array($table . '</tbody></table></div>', p_get_instructions(implode("\n===== ", $newparts)));
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode === 'xhtml') {
            // output table html
            $renderer->doc .= "<div class='dlp_parade'>" . $data[0];
        }
        if ($data != NULL) {
            // render rest in all modes
            $renderer->doc .= p_render($mode, $data[1], $info);
        }
        if ($mode === 'xhtml') {
            // output table html
            $renderer->doc .= "</div>";
        }
        return true;
    }
}

