$(document).ready(function(){
    $('#btnLogin').click(function(){
        //Deklarasi variabel untuk menyimpan nilai inputan
        var email    = $('#email').val();
        var password = $('#password').val();

        // console.log(email);
        // console.log(password);

        //Buat objek untuk menampung data inputan
        var data = {
            email : email,
            password : password
        }

        //kirim data ke server dengan AJAX POST
        $.ajax({
            url : BASE_URL + 'proses/proses_login.php',
            type: 'POST',
            dataType: 'json',
            data : data,
            success:function(response){
                if(response.success === true){
                    Swal.fire({
                        icon: 'success',
                        text: 'Login Berhasil',
                        timer: 1000,
                        showConfirmButton: false 
                    }).then(function(){
                        //Arahkan sesuai role
                        if(response.role === 'ADMIN_MASTER'){
                            window.location.replace(BASE_URL + 'php/dashboard_master.php');
                        }else if(response.role === 'ADMIN'){
                            window.location.replace(BASE_URL + 'php/dashboard_admin.php');
                        }else{

                        }
                    })
                }else{
                    Swal.fire({icon: 'error', text: response.message || 'Username atau Password salah!'});
                    $('#btnLogin').prop('disabled', false);
                }
            },
            error:function(){
                Swal.fire({icon: 'error', text: 'Terjadi kesalahan pada server'});
                $('#btnLogin').prop('disabled', false);
            }
        })
    })
});