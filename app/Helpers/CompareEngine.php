<?php
function wordSimilarity($s1,$s2) {

    $words1 = preg_split('/\s+/',$s1);
    $words2 = preg_split('/\s+/',$s2);
    $diffs1 = array_diff($words2,$words1);
    $diffs2 = array_diff($words1,$words2);

    $diffsLength = strlen(join("",$diffs1).join("",$diffs2));
    $wordsLength = strlen(join("",$words1).join("",$words2));
    if(!$wordsLength) return 0;

    $differenceRate = ( $diffsLength / $wordsLength );
    $similarityRate = 1 - $differenceRate;
    return $similarityRate;

}
