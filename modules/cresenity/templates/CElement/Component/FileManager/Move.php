<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 10:03:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
?>
<ul class="nav nav-pills flex-column">
    <?php foreach ($rootFolders as $rootFolder): ?>
        <li class="nav-item">
            <a class="nav-link can-click" href="#" data-type="0" data-url="<?php echo $rootFolder->url; ?>">
                <i class="fa fa-folder fa-fw"></i> <?php echo $rootFolder->name; ?>
                <input type="hidden" id="goToFolder" name="goToFolder" value="<?php echo $rootFolder->url; ?>">
                <div id="items">
                    <?php foreach ($items as $i): ?>
                        <input type="hidden" id="<?php echo $i; ?>" name="items[]" value="<?php echo $i; ?>">
                    <?php endforeach; ?>
                </div>
            </a>
        </li>
        <?php foreach ($rootFolder->children as $directory): ?>
            <li class="nav-item sub-item">
                <a class="nav-link can-click" href="#" data-type="0" data-url="<?php echo $directory->url; ?>">
                    <i class="fa fa-folder fa-fw"></i> <?php echo $directory->name; ?>
                    <input type="hidden" id="goToFolder" name="goToFolder" value="<?php echo $directory->url; ?>">
                    <div id="items">
                        <?php foreach ($items as $i): ?>
                            <input type="hidden" id="<?php echo $i; ?>" name="items[]" value="<?php echo $i; ?>">
                        <?php endforeach; ?>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    <?php endforeach; ?>
</ul>

<script>

    $('.can-click').click(function () {
        var folder = $(this).attr('data-url');
        $("#notify").modal('hide');
        var items = [];
        $("#items").find("input").each(function () {
            items.push(this.id)
        });
        performFmRequest('doMove', {
            items: items,
            goToFolder: folder
        }).done(refreshFoldersAndItems);
    });

</script>