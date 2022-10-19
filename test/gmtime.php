<?php
echo time();
echo '<br />';
function gmtime()
{
    return (time() - date('Z'));
}
echo gmtime();
?>
