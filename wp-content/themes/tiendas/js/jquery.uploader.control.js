class UploadController {
    constructor(selector, loadFrom, storeId) {
        this.uploadField = jQuery(selector);
        this.previewContainer = this.uploadField.find('.field-upload-content');
        this.fileInputID = this.uploadField.find('.field-upload-input').attr('id');
        this.fileInput = this.uploadField.find('.field-upload-input');
        this.clearButton = this.uploadField.find('.image-preview-close');
        this.existingFiles = []; // Array to hold existing files
        this.createdObjectUrls = []; // Track created Blob URLs to revoke and prevent memory leaks
        this.loadFrom = loadFrom;
        this.storeId = storeId;
        this.previewIndex = 0;
        this.init();
    }

    init() {
        this.bindEvents();
        if(this.storeId != null){
            this.fetchExistingFiles(); // Fetch existing files from the database
        }
    }

    updateIndex(newIndex) {
        this.loadFrom[1] = newIndex;
    }

    updateStoreId(storeId) {
        this.storeId = storeId;
    }

    updatePreviewIndex(newPreviewIndex) {
        this.previewIndex = newPreviewIndex;
    }

    getUploadedFiles() { return this.existingFiles; }

    fetchExistingFiles() {
        jQuery.ajax({
            url: my_ajax_obj.ajax_url,
            method: 'POST',
            data: {
                action: 'ws', 
                wsa: 'get-section-files',
                from: this.loadFrom, 
                store_id: this.storeId,
                nonce: typeof my_ajax_obj !== 'undefined' ? my_ajax_obj.nonce : ''
            },
            success: (data) => {
                if(data.files != undefined) {
                    this.existingFiles = data.files;
                    this.processFiles();
                }
            },
            error: (error) => {
                popup_message(my_ajax_obj.error_db_files+' '+error, 'error', 2000);
            }
        });
    }

    async processFiles() {

        var dataFiles = this.existingFiles;
        this.existingFiles = [];

        // Use map to create an array of promises
        const filePromises = dataFiles.map(file => 
            this.createFileFromUrl(file.url, file.name).then(fileOBJ => {
                fileOBJ.url = file.url;
                this.existingFiles.push(fileOBJ);
            })
        );
        
        // Wait for all files to be created
        await Promise.all(filePromises);

        this.displayExistingFiles();
        this.uploadField.find('input[type="hidden"]').val(1);
    
        // Now you can continue with the rest of your code

        // Create a DataTransfer to hold the files
        const dataTransfer = new DataTransfer();
        this.existingFiles.forEach(file => {
            dataTransfer.items.add(file); // Add each File object to the DataTransfer
        });

        const htmlfileInput = document.getElementById(this.fileInputID);
        htmlfileInput.files = dataTransfer.files;
    }

    async createFileFromUrl(fileUrl, fileName) {
        try {
            // Step 1: Fetch the file data from the URL
            const response = await fetch(fileUrl);
            if (!response.ok) {
                throw new Error(my_ajax_obj.network_failure);
            }
    
            // Step 2: Convert the response to a Blob
            const blob = await response.blob();
    
            // Step 3: Create a File object from the Blob
            const file = new File([blob], fileName, { type: blob.type });
    
            return file; // Return the File object
        } catch (error) {
            popup_message(my_ajax_obj.error_link_file+' '+error, 'error', 2000);
        }
    }

    displayExistingFiles() {
        this.previewContainer.find('.img-preview-container').remove(); // Clear previous previews
        this.existingFiles.forEach(file => {
            this.displayPreview(file, file.url); // Assuming each file object has a name and a URL
        });
        this.previewContainer.fadeIn();
        this.clearButton.fadeIn();
        this.uploadField.find('.field-upload-field').hide();
        this.uploadField.find('input[type="hidden"]').val(0);
        this.previewContainer.css({
            'z-index': '6'
        });
    }

    bindEvents() {
        this.uploadField.find('.field-upload-overlay').on('click', () => {
            this.fileInput.trigger('click');
        });
        this.fileInput.on('change', (e) => this.handleFileSelect(e));
        this.uploadField.find('.field-upload-overlay').on('dragenter', (e) => e.preventDefault());
        this.uploadField.find('.field-upload-overlay').on('dragover', (e) => e.preventDefault());
        this.uploadField.find('.field-upload-overlay').on('drop', (e) => this.handleFileDrop(e));
        this.previewContainer.on('drop', (e) => this.handleFileDrop(e));
        this.clearButton.on('click', () => {
            this.clearFiles();
        });
    }

    clearFiles() {
        var htmlfileInput = document.getElementById(this.fileInputID);

        const dataTransfer = new DataTransfer();

        htmlfileInput.files = dataTransfer.files;
        const eventoChange = new Event('change', {
            bubbles: true,
            cancelable: true
        });

        // Lanzar el evento en el input
        htmlfileInput.dispatchEvent(eventoChange);
        
        // Revoke created Blob URLs to release browser memory
        if (Array.isArray(this.createdObjectUrls)) {
            this.createdObjectUrls.forEach(url => URL.revokeObjectURL(url));
            this.createdObjectUrls = [];
        }

        this.existingFiles = []; // Clear existing files

        this.previewContainer.find('.img-preview-container').remove();
        this.previewContainer.hide();
        this.clearButton.hide();
        this.uploadField.find('.field-upload-field').fadeIn();
        this.uploadField.find('input[type="hidden"]').val(0);
        this.previewContainer.css({
            'z-index': '5'
        });
    }

    handleFileSelect(event) {
        event.preventDefault();
        event.stopPropagation();

        const files = event.target.files;
        
        this.addFiles(files);
    }

    handleFileDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const files = event.originalEvent.dataTransfer.files;
        const htmlfileInput = document.getElementById(this.fileInputID);
        htmlfileInput.files = mergeFileLists(htmlfileInput.files, files);

        this.addFiles(files);
    }

    addFiles(files) {
        if (this.existingFiles.length + files.length > 5) {
            popup_message(my_ajax_obj.max_files, 'warning', 2000);
            return;
        }

        // this.previewContainer.find('.img-preview-container').remove(); // Clear previous previews

        for (let i = 0; i < files.length && i < 5; i++) {
            const file = files[i];
            if (this.isValidFileType(file.type)) {
                this.readFile(file);

                this.previewContainer.fadeIn();
                this.clearButton.fadeIn();
                this.uploadField.find('.field-upload-field').hide();
                this.uploadField.find('input[type="hidden"]').val(0);
                this.previewContainer.css({
                    'z-index': '6'
                });
            } else {
                popup_message(my_ajax_obj.unsupported_file_type, 'error', 2000);
                this.clearButton.trigger('click');
            }
        }
    }

    isValidFileType(type) {
        // console.log(type);
        
        const validTypes = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
            'application/vnd.oasis.opendocument.text',
            'application/msword',
            'application/vnd.ms-excel',
            'application/pages',
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/mp4',
            'video/mov',
            'video/quicktime'
        ];
        return validTypes.includes(type);
    }

    readFile(file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            
            // console.log(this.storeId, this.loadFrom)
            // if(this.storeId == null && this.loadFrom != ''){
            if(this.loadFrom != ''){
                file.index = this.loadFrom[0]+this.loadFrom[1];
                file.data = event.target.result;
            }

            this.existingFiles.push(file); // Add to the existing files array
            this.displayPreview(file, event.target.result);
        };

        reader.readAsDataURL(file);
    }

    displayPreview(file, data) {
        const fileTypeIcons = {
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'docx.svg',
            'text/plain': 'txt.svg',
            'application/vnd.oasis.opendocument.text': 'odt.svg',
            'application/msword': 'doc.svg',
            'application/vnd.ms-excel': 'excel.svg',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'excel.svg',
            'text/csv': 'excel.svg',
            'application/pages': 'pages.svg',
            'application/pdf': 'pdf.svg',
            'image/jpeg': 'img.svg',
            'image/png': 'img.svg',
            'image/gif': 'img.svg',
            'video/mp4': 'video.svg',
            'video/mov': 'video.svg',
            'video/quicktime': 'video.svg'
        };

        const icon = fileTypeIcons[file.type] || 'default.svg';
        const fileType = file.type;
        
        var previewHtml = '';

        if(fileType.startsWith('image/')){
            previewHtml = `
                <div class="img-preview-container" preview-index="${this.previewIndex}">
                    <a href="${data}" data-fancybox="image" target="_blank">
                        <img src="${data}" class="img-preview" alt="">
                        <small>${this.truncateString(file.name, 14)}</small>
                    </a>
                </div>
            `;

            this.previewContainer.append(previewHtml);

            Fancybox.bind('[data-fancybox="image"]', {
                hideScrollbar: false,
                Thumbs: false,
                Carousel: {
                    transition: "fade",
                },
                Toolbar: {
                    display: {
                        left: [],
                        middle: [],
                        right: ["close"],
                    },
                },
            });
        }else if(fileType.startsWith('application/')){

            var pindex = this.previewHtml;
            const fileURL = URL.createObjectURL(file);
            const windowWidth = jQuery(window).width();

            var dataFB = '';

            if(windowWidth > 1200){
                dataFB = 'data-fancybox="iframe" data-width="1024" data-height="720"';
            }else if(windowWidth > 768){
                dataFB = 'data-fancybox="iframe" data-width="992" data-height="620"';
            }else{
                dataFB = 'data-fancybox="iframe" data-width="640" data-height="360"';
            }

            // <div class="img-preview">Loading...</div>

            previewHtml = `
                <div class="img-preview-container" preview-index="${this.previewIndex}">
                    <a href="${fileURL}" title="${file.name}" target="_blank">
                        <img src="${my_ajax_obj.base_url}/wp-content/themes/tiendas/img/${icon}" class="img-preview icon" alt="">
                        <small>${this.truncateString(file.name, 14)}</small>
                    </a>
                </div>
            `;

            this.previewContainer.append(previewHtml);

            Fancybox.bind('[data-fancybox="iframe"]', {
                hideScrollbar: false,
                Thumbs: false,
                defaultType: 'iframe',
                Carousel: {
                    transition: "fade",
                },
                Toolbar: {
                    display: {
                        left: [],
                        middle: [],
                        right: ["close"],
                    },
                },
            });

            // console.log(data);

            // mammoth.convertToHtml({arrayBuffer: data}).then(function(html){
            //     this.previewContainer.find('[preview-index="'+pindex+'"]').find('.img-preview').html(html);
            // }).catch(function(err) {
            //     console.error(err);
            // });
        }else if(fileType.startsWith('video/')){

            const fileURL = URL.createObjectURL(file);
            const windowWidth = jQuery(window).width();

            var dataFB = '';

            if(windowWidth > 1200){
                dataFB = 'data-fancybox="video" data-width="1024" data-height="720"';
            }else if(windowWidth > 768){
                dataFB = 'data-fancybox="video" data-width="992" data-height="620"';
            }else{
                dataFB = 'data-fancybox="video" data-width="640" data-height="360"';
            }

            previewHtml = `
                <div class="img-preview-container" preview-index="${this.previewIndex}">
                    <a href="${fileURL}" ${dataFB} target="_blank">
                        <video class="img-preview" src="${fileURL}" autoplay loop muted></video>
                        <small>${this.truncateString(file.name, 14)}</small>
                    </a>
                </div>
            `;

            this.previewContainer.append(previewHtml);

            Fancybox.bind('[data-fancybox="video"]', {
                hideScrollbar: false,
                Thumbs: false,
                defaultType: 'iframe',
                Carousel: {
                    transition: "fade",
                },
                Toolbar: {
                    display: {
                        left: [],
                        middle: [],
                        right: ["close"],
                    },
                },
            });
        }else{
            previewHtml = `
                <div class="img-preview-container" preview-index="${this.previewIndex}">
                    <a href="${data}" target="_blank">
                        <img src="${my_ajax_obj.base_url}/wp-content/themes/tiendas/img/${icon}" class="img-preview icon" alt="">
                        <small>${this.truncateString(file.name, 14)}</small>
                    </a>
                </div>
            `;
            this.previewContainer.append(previewHtml);
        }

        this.updatePreviewIndex(this.previewIndex + 1);
    }

    truncateString(str, num) {
        return truncateString(str, num)
    }
}