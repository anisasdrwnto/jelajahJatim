$(document).ready(function(){
    $('#btnRegister').click(function(){
        //Deklarasi variabel untuk menyimpan nilai inputan
        var nama            = $('#nama').val();
        var email           = $('#email').val();
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();

        //Buat objek untuk menampung data inputan
        var data = {
            nama  : nama,
            email : email,
            password : password,
            confirm_password : confirmPassword
        };

        //kirim data ke server dengan AJAX POST
        $.ajax({
            url : BASE_URL + 'proses/proses_register.php',
            type: 'POST',
            dataType: 'json',
            data : data,
            success:function(response){
                if(response === true){
                    Swal.fire({
                        icon: 'success',
                        text: 'Akun Anda berhasil dibuat!',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(()=>{
                        window.location.replace(BASE_URL + 'php/index.php');
                    });
                }else if(response.includes("email")){
                    Swal.fire({icon: 'error', text: 'Email sudah terdaftar!'});
                }
            },
            error:function(){
                Swal.fire({icon: 'error', text: 'Akun gagal didaftarkan!'});
            }
        })
    })
})