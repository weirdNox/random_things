<?php
require_once 'php/Membership.php';
require_once 'php/document.php';

$membership = new Membership();
$info = $membership->verifyInfo();
showHeader($info, 0);
?>
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Carregar saldo</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
                	<form action="/settings.php" method="post">	
                        <fieldset>
                        <?php
						
						if($_GET && !empty($_GET['VCode']) && is_numeric($_GET['VCode'])) {
							if($_GET['VCode'] == -1) {
								echo "<div class='round err-box'>Esse código não está associado a nenhum voucher.</div>";
							} else if($_GET['VCode'] < -1) {
								echo "<div class='round err-box'>Ocorreu um erro.</div>";
							} else {
								echo "<div class='round confirm-box'>Carregámos o seu saldo com ".$_GET['VCode']." euros.</div>";
							}
						}
						
						?>
                            <input type="hidden" value="updateMoney" name="func" id="func" />
                            <p>
                                <label for="VCode">Código do Voucher</label>
                                <input type="text" id="VCode" name="VCode" class="round default-width-input" autocomplete="off" />
                            </p>
                            
                            <input type="submit" value="Carregar" class="button round green image-right ic-arrow-right" /></fieldset>
                            
                        </fieldset>
                    </form>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
        	<?php
			if($info[7]) {?>
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Eventos na turma</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
					<iframe src="https://www.google.com/calendar/embed?showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;showTz=0&amp;height=600&amp;wkst=2&amp;hl=pt_PT&amp;bgcolor=%23FFFFFF&amp;src=35suil1r7mf0i9eh4892fuugf8%40group.calendar.google.com&amp;color=%23125A12&amp;src=en.portuguese%23holiday%40group.v.calendar.google.com&amp;color=%232F6309&amp;ctz=Europe%2FLisbon" style=" border-width:0 " width="100%" height="600" frameborder="0" scrolling="no"></iframe>
                </div><!-- End Module Main -->
            </div><!-- End Module --><?php
			}
            ?>
            <!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Atualizações</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main cf" id="posts">
                    
                    <input type="submit" class="button round green image-right ic-arrow-right center" id="morePosts" value="Mais atualizações" />
                    <?php
					if($info[6]) {
						echo '<h2><label for="postContent">Escrever nova atualização:</label>
						<textarea id="postContent" name="postContent" class="round full-width-textarea" rows="10"></textarea></h2>
						<input type="submit" value="Atualizar" 
							class="button round green image-right ic-arrow-right fr" id="postSubmit" />';
							
						echo '<div id="postError" fl></div>';
					}
					?>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
            <script src="/js/posts.js"></script>
<?php
showFooter();