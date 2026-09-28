<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('brochure/css/style.css')); ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
</head>

<body>
    <?php
        $agentImage = $property->agent?->pp_enabled == 1 ? $property->agent?->pp_photo : $property->agent?->image_url;
        $agentImage = $agentImage != null ? $agentImage : $property->agent?->image_url;
    ?>




    <main>
        <div class="container-a4-size">
            <div class="container-a4">
                <div class="container">
                    <?php if(isset($images)): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex img-container">
                                    <div class="main-img"
                                        style='background-image: url("<?php echo e($images[0]?->thumbnail); ?>"); background-size: cover;'>
                                        <?php if($property?->qr_link): ?>
                                            <img src="<?php echo e($property?->qr_link); ?>" class="qr-code" alt="Marina">
                                        <?php endif; ?>
                                    </div>
                                    <div class="three-img">
                                        <img src="<?php echo e($images[1]?->thumbnail); ?>">
                                        <img src="<?php echo e($images[2]?->thumbnail); ?>">
                                        <img src="<?php echo e($images[3]?->thumbnail); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex img-container">
                                    <div class="main-img"
                                        style='background-image: url("<?php echo e($image->original ?? $property?->photos[0]->original); ?>"); background-size: cover;'>
                                        <img src="images/qr.png" class="qr-code" alt="Marina">
                                    </div>
                                    <div class="three-img">
                                        <img src="<?php echo e($property?->photos[1]->path); ?>">
                                        <img src="<?php echo e($property?->photos[2]->path); ?>">
                                        <img src="<?php echo e($property?->photos[3]->path); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-block-header-top d-flex justify-content-between">
                                <div class="left">
                                    <h1><?php echo e($property->propertyCategory->title); ?></h1>
                                    <p> <?php echo e($property->title); ?></p>
                                </div>
                                <div class="right">
                                    <?php if(in_array($property->for, ['Residential for rent', 'Commercial for rent'])): ?>
                                    <h2 <?php if($show_price == true): ?>  <?php else: ?> style="visibility: hidden" <?php endif; ?>>
                                            AED <?php echo e(number_format($property->price?->price)); ?> /year</h2>
                                    <?php else: ?>
                                    <h2 <?php if($show_price == true): ?>  <?php else: ?> style="visibility: hidden" <?php endif; ?>>
                                        AED <?php echo e(number_format($property->price?->price)); ?></h2>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-block-header-bottom  d-flex justify-content-between">
                                <div class="left">
                                    <p>Address:
                                        <?php echo e($property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name); ?>

                                    </p>
                                </div>
                                <div class="right">
                                    <p><?php echo e($property->for); ?><br><?php echo e($property->ref_no); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="hr-line">
                    <div class="row">
                        <div class="col-md-12"  style="min-height: 212px;max-height: 213px">
                            <div class="details d-flex align-items-center justify-content-between">
                                <div class="left">
                                    <ul>
                                        

                                        <?php if($amenities): ?>

                                            <?php
                                                $amenities = explode(',', $amenities);
                                            ?>
                                            <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class=""><img src="<?php echo e(asset('brochure/images/check-mark.png')); ?>"><?php echo e($amenity); ?>

                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <?php $__currentLoopData = $property?->amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenities): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($amenities?->amenity != null): ?>
                                                    <li><img
                                                            src="<?php echo e(asset('brochure/images/check-mark.png')); ?>"><?php echo e($amenities?->amenity?->title); ?>

                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                        
                                    </ul>
                                </div>
                                <div class="right">
                                    <div class="item">
                                        <img src="<?php echo e(asset('brochure/images/icon1.png')); ?>">
                                        <?php if($property?->bed?->name == 'Studio'): ?>
                                            <span>Studio</span>
                                        <?php else: ?>
                                            <span><?php echo e($property?->bed?->name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item">
                                        <img src="<?php echo e(asset('brochure/images/icon2.png')); ?>">
                                        <span><?php echo e($property?->bath?->name); ?></span>
                                    </div>
                                    <div class="item">
                                        <img src="<?php echo e(asset('brochure/images/icon3.png')); ?>">
                                        <span><?php echo e($property->built_up_area); ?></span>
                                    </div>
                                    <div class="item">
                                        <img src="<?php echo e(asset('brochure/images/icon4.png')); ?>">
                                        <span><?php echo e(ucfirst($property->furnished)); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                <div class="container-a4-footer">
                    <?php
                        if ($profile_user == 'agent') {
                            $agentImage =
                                $property->agent?->pp_enabled == 1
                                    ? $property->agent?->pp_photo
                                    : $property->agent?->image_url;
                            $agentImage = $agentImage != null ? $agentImage : $property->agent?->image_url;
                        } else {
                            $agentImage = auth()->user()?->image_url;
                            $agentImage = $agentImage != null ? $agentImage : auth()->user()?->image_url;
                        }
                    ?>

                    <div class="container" <?php if($show_profile): ?> <?php else: ?> style="visibility: hidden" <?php endif; ?>>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="agent-details d-flex justify-content-between align-items-center">
                                    <div class="agent d-flex">
                                        <div class="img">
                                            <img src="<?php echo e($agentImage); ?>">
                                        </div>
                                        <div class="info">
                                            <h4>
                                                <?php if($profile_user == 'agent'): ?>
                                                    <?php echo e($property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name); ?>

                                                <?php else: ?>
                                                    <?php echo e(auth()->user()?->name); ?>

                                                <?php endif; ?>
                                            </h4>
                                            <div class="contact-details d-flex">
                                                <div class="left">
                                                    <p>
                                                        <?php if($profile_user == 'agent'): ?>
                                                            <?php echo e($property->agent?->position ?? ''); ?>

                                                        <?php else: ?>
                                                            <?php echo e(auth()->user()?->position ?? ''); ?>

                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <div class="right">
                                                    <p>
                                                        <span>
                                                            <?php if($profile_user == 'agent'): ?>
                                                                +<?php echo e(numbers_only($property->agent?->mobile) ?? ''); ?>

                                                            <?php else: ?>
                                                                +<?php echo e(numbers_only(auth()->user()?->mobile) ?? ''); ?>

                                                            <?php endif; ?>
                                                        </span>
                                                        <span>
                                                            <img src="<?php echo e(asset('brochure/images/tg.png')); ?>">
                                                            <img src="<?php echo e(asset('brochure/images/wa.png')); ?>">
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <?php if($profile_user == 'agent'): ?>
                                                            <?php echo e($property->agent?->email ?? ''); ?>

                                                        <?php else: ?>
                                                            <?php echo e(auth()->user()?->email ?? ''); ?>

                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="company-logo">
                                        <img src="<?php echo e(asset('brochure/images/logo.png')); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        document.getElementById('downloadPDF').addEventListener('click', function() {
            var element = document.querySelector('.container-a4-size');
            console.log('Element to convert:', element);

            // Опции для конвертации
            var opt = {
                // margin: 0.5,
                margin: 0,
                filename: 'myfile.pdf',
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                console.log('PDF generated successfully.');
            }).catch((error) => {
                console.error('Error generating PDF:', error);
            });
        });
    </script>
</body>

</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/pdf/index.blade.php ENDPATH**/ ?>