<?php
declare(strict_types=1);
?>

<div class="jst-alert Default <?=$stack['type']?>">

   <span class="jst-head">
        <?=$stack['code']?>
        <?=strtoupper($stack['type'])?>
        <a href="https://stackoverflow.com/search?q=[php] <?=$stack['message']?>" class="so-link">&#9906;</a>
   </span>

   <span class="jst-head jst-right">
        <?=$stack['mode']?>
   </span>

   <span class="jst-message"><br><br>
        <?=$stack['message']?>
   </span><br><br>

   <div class="jst-preview">
        <span class="jst-file">
            <?=$stack['file']?>
        </span><br>
        <code>
        <?=$stack['preview']?>
        </code>
   </div>

   <div class="jst-trace">
        <?=nl2br($stack['trace'])?>
   </div><br>

</div>