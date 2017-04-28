<?php
function showRegisterForm($errors = "default") {
	if($errors != 'none') {
		$content = '<form method="post" action="/register.php?register" id="login-form" accept-charset="UTF-8"><fieldset>';
		
			if($errors != 'notCompleted' && $errors != "none" && $errors != "default"){
				$content .= '<div class="round content-box err-box"><ul>';
				foreach($errors as $error)
					  $content .= '<li class="left-margin">' . $error . '</li>';
				$content .= '</ul></div>';
			} else if($errors == 'notCompleted')
				$content .= '<div class="round content-box err-box">Por favor, preencha tudo.</div>';
		$content .= '<input type="hidden" name="submited" value="true" />
				<p>
					<label for="register-nick">Nome de Utilizador</label>
					<input type="text" id="register-nick" class="round full-width-input" name="nick" autofocus="autofocus" />
					<em>Nome que tu vais usar para fazeres login</em>
				</p>
				
				<p>
					<label for="register-password">Password</label>
					<input type="password" id="register-password" class="round full-width-input" name="password" autocomplete="off" />
					<em>Nunca reveles a tua password!</em>
				</p>
				
				<p>
					<label for="register-fname">Primeiro Nome</label>
					<input type="text" id="register-fname" class="round full-width-input" name="fname" />
				</p>
				
				<p>
					<label for="register-lname">Último Nome</label>
					<input type="text" id="register-lname" class="round full-width-input" name="sname" />
				</p>
				
				<p>
					<label for="register-email">Email</label>
					<input type="text" id="register-email" class="round full-width-input" name="email" />
					<em>Põe o teu email verdadeiro, vais precisar dele para ativares a tua conta.</em>
				</p>
				
				<a href="login.php" class="button ic-arrow-left image-left round green text-upper white-text bold">Voltar</a>
				<input type="submit" name="register" value="Registar" class="button ic-arrow-right image-right round green text-upper white-text bold" />
				
			</fieldset> <!-- End fieldset-->
		</form>';
	} else {
		header('location: /thankyou.php?registed');
	}

	echo $content;
}