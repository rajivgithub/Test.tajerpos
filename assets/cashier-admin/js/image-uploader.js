/*! Image Uploader - v1.2.3 - 26/11/2019
 * Copyright (c) 2019 Christian Bayer; Licensed MIT */

(function ($) {

    $.fn.imageUploader = function (options) {
        $.fn.imageUploader.getImages = function () {
            let images = [];
            let maxPreloadedId = -1;
            let nonPreloadedStartId = 0;
            // Iterate over each image uploader instance
            $('.image-uploader').each(function () {
              let $container = $(this);
              let $uploadedImages = $container.find('.uploaded-image');
              let id = 0;
              $uploadedImages.each(function () {
                let $imgContainer = $(this);
                let src = $imgContainer.find('img').attr('src');
                let originalFileName = $imgContainer.find('img').attr('alt');
                let mode = $imgContainer.find('.delete-image').data('mode') || '';
                imageID = $imgContainer.find('.delete-image').data('image-id') || '';
                let preloaded = $imgContainer.data('preloaded') === true;
                if (preloaded) {
                    id = $imgContainer.find('input[type="hidden"]').val();
                } else {
                    id = parseInt(id) +parseInt(1);
                }
                images.push({
                  src: src,
                  originalFileName: originalFileName,
                  mode: mode,
                  preloaded: preloaded,
                  productImageID : imageID,
                  id: id
                });
              });
            });
      
            return images;
        };
      

        // Default settings
        let defaults = {
            preloaded: [],
            imagesInputName: 'images',
            preloadedInputName: 'preloaded',
            label: 'Drag & Drop files here or click to browse',
            extensions: ['.jpg', '.jpeg', '.png'],
            mimes: ['image/jpeg', 'image/png'],
            maxSize: undefined,
            maxFiles: undefined,
            imagesInputClass: '',
            dataLabel : '',
            dataType : ''
        };

        // Get instance
        let plugin = this;

        // Will keep the files
        let dataTransfer = new DataTransfer();

        // The file input
        let $input;

        // Set empty settings
        plugin.settings = {};

        // Plugin constructor
        plugin.init = function () {

            // Define settings
            plugin.settings = $.extend(plugin.settings, defaults, options);

            // Run through the elements
            plugin.each(function (i, wrapper) {

                // Create the container
                let $container = createContainer();

                // Append the container to the wrapper
                $(wrapper).append($container);

                // Set some bindings
                $container.on("dragover", fileDragHover.bind($container));
                $container.on("dragleave", fileDragHover.bind($container));
                $container.on("drop", fileSelectHandler.bind($container));

                // If there are preloaded images
                if (plugin.settings.preloaded.length) {

                    // Change style
                    $container.addClass('has-files');

                    // Get the upload images container
                    let $uploadedContainer = $container.find('.uploaded');

                    // Set preloaded images preview
                    for (let i = 0; i < plugin.settings.preloaded.length; i++) {
                        $uploadedContainer.append(createImg(plugin.settings.preloaded[i].src, plugin.settings.preloaded[i].id, true,plugin.settings.preloaded[i].mode,'',plugin.settings.preloaded[i].image_id));
                    }

                }

            });

        };

        let createContainer = function () {

            // Create the image uploader container
            let $container = $('<div>', {class: 'image-uploader'});

            // Create the input type file and append it to the container
            $input = $('<input>', {
                type: 'file',
                id: plugin.settings.imagesInputName + '-' + random(),
                name: plugin.settings.imagesInputName + '[]',
                class: plugin.settings.imagesInputClass, 
                "data-label": plugin.settings.dataLabel,
                "data-type": plugin.settings.dataType,
                accept: plugin.settings.extensions.join(','),
                multiple: ''
            }).appendTo($container);

            // Create the uploaded images container and append it to the container
            let $uploadedContainer = $('<div>', {class: 'uploaded'}).appendTo($container),

                // Create the text container and append it to the container
                $textContainer = $('<div>', {
                    class: 'upload-text'
                }).appendTo($container),

                // Create the icon and append it to the text container
                $i = $('<i>', {class: 'iui-cloud-upload'}).appendTo($textContainer),

                // Create the text and append it to the text container
                $span = $('<span>', {text: plugin.settings.label}).appendTo($textContainer);


            // Listen to container click and trigger input file click
            $container.on('click', function (e) {
                // Prevent browser default event and stop propagation
                prevent(e);

                // Trigger input click
                $input.trigger('click');
            });

            // Stop propagation on input click
            $input.on("click", function (e) {
                e.stopPropagation();
            });

            // Listen to input files changed
            $input.on('change', fileSelectHandler.bind($container));

            return $container;
        };


        let prevent = function (e) {
            // Prevent browser default event and stop propagation
            e.preventDefault();
            e.stopPropagation();
        };

        let createImg = function (src, id, preloaded, mode = '', originalFileName = '',image_id = '') {
            // Create the uploaded image container
            let $container = $('<div>', {class: 'uploaded-image'}),
                // Create the img tag
                $img = $('<img>', {src: src, alt: originalFileName}).appendTo($container),
                // Create the delete button
                $button = $('<button>', {class: 'delete-image delete-product-image', 'data-mode': mode,'data-image-id':image_id}).appendTo($container),
                // Create the delete icon
                $i = $('<i>', {class: 'iui-close delete-product-img'}).appendTo($button);
        
            // If the image is preloaded
            if (preloaded) {
                // Set a identifier
                $container.attr('data-preloaded', true);
                // Create the preloaded input and append it to the container
                let $preloaded = $('<input>', {
                    type: 'hidden',
                    name: plugin.settings.preloadedInputName + '[]',
                    value: id
                }).appendTo($container);
            } else {
                // Set the index
                $container.attr('data-index', id);
            }
        
            // Stop propagation on click
            $container.on("click", function (e) {
                // Prevent browser default event and stop propagation
                prevent(e);
            });
            deleteImageIDs = [];
            // Set delete action
            $button.on("click", function (e) {
                // Prevent browser default event and stop propagation
                prevent(e);
                _this = $(this);
                var imageUrl = baseUrl + "/assets/cashier-admin/images/upload-img.png";
                // Get the parent element
                let $parent = $container.parent();
                // If is not a preloaded image
                if ($container.data('preloaded') === true) {
                    // Remove from preloaded array
                    plugin.settings.preloaded = plugin.settings.preloaded.filter(function (p) {
                        return p.id !== id;
                    });
                    remove_img_path = $(this).closest(".uploaded-image").find("img").attr("src");
                    mode = $(this).attr("data-mode");
                    if (mode == "add") removeProductImg(remove_img_path, $(this));
                    if(mode == "edit") {
                        image_id = $(this).data("image-id");
                        deleteImageIDs.push(image_id);
                        $(this).closest("form").find(".remove-image-ids").val(JSON.stringify(deleteImageIDs));
                        $(this).closest("form").find(".variants-tbody").find(".variant-selected-images").each(function() {
                            variantImages = $(this).val() != "" ? JSON.parse($(this).val()) : [];
                            if(variantImages != undefined && variantImages.length > 0) {
                                variantImages = variantImages.filter(function (item) {
                                    return $.inArray(item.productImageID, deleteImageIDs) === -1;
                                });
                                $(this).closest("tr").find(".variant-selected-images").val(JSON.stringify(variantImages));
                            }
                        });
                        $(this).closest("form").find(".variants-tbody").find(".variant-selected-images").each(function() {
                            _this = $(this);
                            variantImages = $(this).val() != "" ? JSON.parse($(this).val()) : [];
                            if(variantImages != undefined && variantImages.length > 0) {
                                $(variantImages).each(function(key,val) {
                                    if(key == 0) {
                                        _this.closest("td").find(".variants-image").attr("src",val.src); 
                                    }
                                });
                            } else 
                                _this.closest("td").find(".variants-image").attr("src",imageUrl); 
                        });
                    }
                } else {
                    // Get the image index
                    let index = parseInt($container.data('index'));
                    // Update other indexes
                    $parent.find('.uploaded-image[data-index]').each(function (i, cont) {
                        if (i > index) {
                            $(cont).attr('data-index', i - 1);
                        }
                    });
                    // Remove the file from input
                    dataTransfer.items.remove(index);
                    // Update input files
                    $input.prop('files', dataTransfer.files);
                }
                // Remove this image from the container
                $container.remove();
                // If there is no more uploaded files
                if (!$parent.children().length) {
                    // Remove the 'has-files' class
                    $parent.parent().removeClass('has-files');
                    $parent.closest("body").find(".product-image").removeClass("optional-field").addClass("required-field");
                }
            });
        
            return $container;
        };
        

        let fileDragHover = function (e) {

            // Prevent browser default event and stop propagation
            prevent(e);

            // Change the container style
            if (e.type === "dragover") {
                $(this).addClass('drag-over');
            } else {
                $(this).removeClass('drag-over');
            }
        };

        let fileSelectHandler = function (e) {

            // Prevent browser default event and stop propagation
            prevent(e);

            // Get the jQuery element instance
            let $container = $(this);

            // Get the files as an array of files
            let files = Array.from(e.target.files || e.originalEvent.dataTransfer.files);

            // Will keep only the valid files
            let validFiles = [];

            // Run through the files
            $(files).each(function (i, file) {
                // Run the validations
                if (plugin.settings.extensions && !validateExtension(file,$container)) {
                    return;
                }
                if (plugin.settings.mimes && !validateMIME(file,$container)) {
                    return;
                }
                if (plugin.settings.maxSize && !validateMaxSize(file,$container)) {
                    return;
                }
                if (plugin.settings.maxFiles && !validateMaxFiles(validFiles.length, file,$container)) {
                    return;
                }
                validFiles.push(file);
            });

            // If there is at least one valid file
            if (validFiles.length) {
                // Change the container style
                $container.removeClass('drag-over');

                // Makes the upload
                setPreview($container, validFiles);
            } else {

                // Update input files (it is now empty due to a default browser action)
                $input.prop('files', dataTransfer.files);

            }
        };

        let validateExtension = function (file, $container) {
            if (plugin.settings.extensions.indexOf(file.name.replace(new RegExp('^.*\\.'), '.')) < 0) {
                $container.closest('.input-field-div').find(".error-message").text(langTranslations.file_extensions_err.replace(':file_name', file.name).replace(':extensions', plugin.settings.extensions.join('", "'))).css("color", "#F30000");
                return false;
            }
            return true;
        };

        let validateMIME = function (file, $container) {
            if (plugin.settings.mimes.indexOf(file.type) < 0) {
                $container.closest('.input-field-div').find(".error-message").text(langTranslations.mime_types_err.replace(':file_name', file.name).replace(':mimes', plugin.settings.mimes.join('", "'))).css("color", "#F30000");
                return false;
            }
            return true;
        };

        let validateMaxSize = function (file, $container) {
            if (file.size > plugin.settings.maxSize) {
                $container.closest('.input-field-div').find(".error-message").text(langTranslations.max_file_size_err.replace(':file_name', file.name).replace(':max', plugin.settings.maxSize / 1024 / 1024)+" MB").css("color", "#F30000");
                return false;
            }
            return true;
        };

        let validateMaxFiles = function (index, file, $container) {
            if ((index + dataTransfer.items.length + plugin.settings.preloaded.length) >= plugin.settings.maxFiles) {
                $container.closest('.input-field-div').find(".error-message").text(langTranslations.max_file_count_err.replace(':file_name', file.name).replace(':max', plugin.settings.maxFiles)+" files was reached").css("color", "#F30000");
                return false;
            }
            return true;
        };

        let setPreview = function ($container, files) {

            // Add the 'has-files' class
            $container.addClass('has-files');

            // Get the upload images container
            let $uploadedContainer = $container.find('.uploaded'),

                // Get the files input
                $input = $container.find('input[type="file"]');

            // Run through the files
            $(files).each(function (i, file) {

                // Add it to data transfer
                dataTransfer.items.add(file);

                // Set preview
                $uploadedContainer.append(createImg(URL.createObjectURL(file), dataTransfer.items.length - 1, false, '', file.name));

            });

            // Update input files
            $input.prop('files', dataTransfer.files);

        };

        // Generate a random id
        let random = function () {
            return Date.now() + Math.floor((Math.random() * 100) + 1);
        };

        this.init();

        // Return the instance
        return this;
    };

}(jQuery));