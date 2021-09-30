<?php
    $uniqueId = rand();
?>
<script>
    function replaceData<?php echo $uniqueId; ?>() {
        const html = <?php echo json_encode($content)?>;
        jQuery('#<?php echo $uniqueId; ?>-data').html(html);
        jQuery('#<?php echo $uniqueId; ?>-data').show();
        jQuery('#<?php echo $uniqueId; ?>-privacy').hide();
    }
</script>
