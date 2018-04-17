<?php
/**
 * @var $table \app\components\cellbrush\Table\Table
 */
?>

<div class="row gutters-tiny js-appear-enabled animated fadeIn" data-toggle="appear">
    <div class="col-6 col-md-4 col-xl-2">
        <a class="block block-link-shadow text-center" href="javascript::void(0)" id="openConstructor">
            <div class="block-content ribbon ribbon-bookmark ribbon-success ribbon-left">
                <div class="ribbon-box">15</div>
                <p class="mt-5">
                    <i class="fa fa-table fa-3x"></i>
                </p>
                <p class="font-w600">Constructor</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a class="block block-link-shadow text-center" href="javascript::void(0)" id="openReportCreate">
            <div class="block-content">
                <p class="mt-5">
                    <i class="fa fa-paint-brush fa-3x"></i>
                </p>
                <p class="font-w600">Create Report</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a class="block block-link-shadow text-center" href="javascript::void(0)">
            <div class="block-content ribbon ribbon-bookmark ribbon-primary ribbon-left">
                <div class="ribbon-box">3</div>
                <p class="mt-5">
                    <i class="si si-bubbles fa-3x"></i>
                </p>
                <p class="font-w600">Forum</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a class="block block-link-shadow text-center" href="javascript::void(0)">
            <div class="block-content">
                <p class="mt-5">
                    <i class="si si-magnifier fa-3x"></i>
                </p>
                <p class="font-w600">Search</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a class="block block-link-shadow text-center" href="javascript::void(0)">
            <div class="block-content">
                <p class="mt-5">
                    <i class="si si-bar-chart fa-3x"></i>
                </p>
                <p class="font-w600">Reports</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a class="block block-link-shadow text-center" href="javascript:void(0)">
            <div class="block-content">
                <p class="mt-5">
                    <i class="si si-settings fa-3x"></i>
                </p>
                <p class="font-w600">Settings</p>
            </div>
        </a>
    </div>
</div>


<script>
    $('#openConstructor').click(function() {
        $.confirm({
            columnClass: 'col-md-12',
            buttons: {
                matrixConstructor: {
                    text: 'Matrix Constructor',
                    keys: ['m'],
                    action: function(){
                        $.alert({
                            columnClass: 'col-md-12',
                            content:'You clicked on "heyThere"'
                        });
                    }
                },
                tabularConstructor: {
                    text: 'Tabular Constructor',
                    keys: ['t'],
                    action: function () {
                        $.alert({
                            columnClass: 'col-md-12',
                            content:'You clicked on wswswswsw'
                        });
                    }
                }
            }
        });
    });
</script>