<?php
require_once './includes/apm.inc.php';
$menu = new Menu();
$menus = $menu->get($username);
?>
<style>
    .home-menu-link:link,
    .home-menu-link:visited,
    .home-menu-link:hover,
    .home-menu-link:active {
        color: #fff;
    }

    .plink:hover {
        background-color: #d9d9d9;
    }
    .pointer {
        cursor: pointer;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Menus</h4>
            <div id="accordion" class="row">
                <?php
                $counter = 1;
                foreach ($menus as $menu) {
                ?>

                    <div class="card col-12 border border-1 p-0">
                        <div class="card-header bg-primary rounded text-center">
                            <a class="card-link d-block home-menu-link" data-toggle="collapse" href="#child<?php echo $counter; ?>" onclick="changeDirectionIcon(this)">
                                <?php echo $menu->display_name ?> <i class="mdi mdi-chevron-down"></i>
                            </a>
                        </div>
                        <div id="child<?php echo $counter; ?>" class="collapse" data-parent="#accordion">
                            <?php
                            if (property_exists($menu, 'children')) {
                            ?>
                                <div class="p-2">
                                    <?php
                                    foreach ($menu->children as $child) {
                                    ?>
                                        <span onclick="<?php echo (Utility::is_new_exam($child->url))?'location.assign(\''.$child->url.'\')':'getPage(\''.$child->url.'\')' ?>" class="d-block p-2 px-3 border border-primary text-primary rounded plink pointer"><?php echo $child->display_name ?></span>
                                    <?php
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>

                        </div>
                    </div>

                <?php
                    $counter++;
                }
                ?>

            </div>
        </div>
    </div>
</div>
<script>
    var open = false;

    function changeDirectionIcon(e) {
        if (open) {
            e.querySelector('i').className = 'mdi mdi-chevron-down';
        } else {
            e.querySelector('i').className = 'mdi mdi-chevron-up'
        }
        open = !open;
    }
</script>