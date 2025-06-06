<script type="text/javascript" nonce="{{ csp_nonce() }}">

    // initialize the validation library
    const validationAddModal = new JustValidate('#modalAddForm', {
          errorFieldCssClass: 'is-invalid',
    });
    // apply rules to form fields
    validationAddModal
      .addField('#heading', [
        {
          rule: 'required',
          errorMessage: 'Heading is required',
        },
        {
            rule: 'customRegexp',
            value: /^[a-z 0-9~%.:_\@\-\/\(\)\\\#\;\[\]\{\}\$\!\&\<\>\?\'\r\n+=,]+$/i,
            errorMessage: 'Heading is invalid',
        },
      ])
      .addField('#image', [
        {
            rule: 'minFilesCount',
            value: 0,
            errorMessage: 'Please select an image',
        },
        {
            rule: 'files',
            value: {
                files: {
                    extensions: ['jpg','jpeg','png', 'webp']
                },
            },
            errorMessage: 'Please select a valid image',
        },
      ])
      .onSuccess(async (event) => {
        // event.target.submit();

        const errorToast = (message) =>{
                iziToast.error({
                    title: 'Error',
                    message: message,
                    position: 'bottomCenter',
                    timeout:7000
                });
            }
            const successToast = (message) =>{
                iziToast.success({
                    title: 'Success',
                    message: message,
                    position: 'bottomCenter',
                    timeout:6000
                });
            }


            var submitBtn = document.getElementById('submitBtnModal')
            submitBtn.innerHTML = `
                <span class="d-flex align-items-center">
                    <span class="spinner-border flex-shrink-0" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                    <span class="flex-grow-1 ms-2">
                        Loading...
                    </span>
                </span>
                `
            submitBtn.disabled = true;

          try {
            var formData = new FormData();
            formData.append('heading',document.getElementById('heading').value)
            formData.append('image_position',document.getElementById('image_position').value)
            formData.append('page_id',{{$page_detail->id}})
            formData.append('description_unformatted',quillDescription.getText())
            formData.append('description',quillDescription.root.innerHTML)
            if(document.getElementById('image').files[0]){
                formData.append('image',document.getElementById('image').files[0])
            }
            // formData.append('refreshUrl','{{URL::current()}}')

            const response = await axios.post('{{route('storePageContent')}}', formData)
            successToast(response.data.message)
            setTimeout(function(){
                window.location.replace(response.data.url);
            }, 1000);
          } catch (error) {
              console.log(error);
            if(error?.response?.data?.errors?.heading){
                validationAddModal.showErrors({'#heading': error?.response?.data?.errors?.heading[0]})
            }
            if(error?.response?.data?.errors?.description_unformatted){
                validationAddModal.showErrors({'#description_unformatted': error?.response?.data?.errors?.description_unformatted[0]})
            }
            if(error?.response?.data?.errors?.description){
                validationAddModal.showErrors({'#description': error?.response?.data?.errors?.description[0]})
            }
            if(error?.response?.data?.errors?.image_position){
                validationAddModal.showErrors({'#image_position': error?.response?.data?.errors?.image_position[0]})
            }
            if(error?.response?.data?.errors?.page_id){
                errorToast(error?.response?.data?.errors?.page_id[0])
            }
            if(error?.response?.data?.errors?.image){
                validationAddModal.showErrors({'#image': error?.response?.data?.errors?.image[0]})
            }
          } finally{
                submitBtn.innerHTML =  `
                    Add
                    `
                submitBtn.disabled = false;
            }
      });
</script>
