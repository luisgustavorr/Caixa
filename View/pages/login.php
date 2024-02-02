
      <div class="bg-img">
         <div class="content_login">
            <header><span>Faça seu <strong class="yellow"> Login</strong></span></header>
            <form method="POST">
               <div class="field">
                  <span class="fa fa-user"></span>
                  <input type="text"name="login" required placeholder="Usuário">
               </div>
               <div class="field space">
                  <span class="fa fa-lock"></span>
                  <input type="password" name="senha" class="pass-key" required placeholder="Código">
                  <span class="show">Mostrar</span>
               </div>
               <div class="pass">
               </div>
               <div class="field">
                  <input type="submit" name="logar" value="LOGIN">
               </div>
            </form>
       
           
           
         </div>
      </div>
  <?php \Models\LoginModel::enviarFormulario()?>

  <script src="<?php echo INCLUDE_PATH ?>js/jquery.js" ></script>

      <script>
         const pass_field = document.querySelector('.pass-key');
         const showBtn = document.querySelector('.show');
         showBtn.addEventListener('click', function(){
          if(pass_field.type === "password"){
            pass_field.type = "text";
             $('.show').html('<i class="fa-regular fa-eye-slash"></i>');
            showBtn.style.color = "#3498db";
          }else{
            pass_field.type = "password";
             $('.show').html('<i class="fa-regular fa-eye"></i>');
            showBtn.style.color = "#222";
          }
         });
      </script>
   </body>
</html>