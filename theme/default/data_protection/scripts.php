<script src="<?php echo $MC_URL?>/vendor/js/getJS.php"></script>
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
