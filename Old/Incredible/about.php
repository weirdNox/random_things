<?php
require_once 'php/Membership.php';
require_once 'php/document.php';

$membership = new Membership();
$info = $membership->verifyInfo();
showHeader($info, 4);
?>
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Como comprar resumos?</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">
					<p><b>O sistema de compra de resumos</b><br />A Incredible Community tem um sistema de compra de resumos simples. Basicamente, se o utilizador tiver saldo para um determinado resumo, pode comprá-lo. Regra geral, os resumos custarão 0,80€. Apenas os resumos extensos, como a famosa gramática de Português (que contém a matéria de 7º, 8º e 9º anos num só resumo), custarão mais do que 0,80€. O material fornecido pelos professores e alguns resumos auxiliares escritos por nós serão de graça.</p><br />
                    
                    <p><b>Como carregar o saldo?</b><br />Devido à maioria dos pais dos nossos clientes não gostar da ideia de carregar o saldo através da internet (como por exemplo paypal), nós decidimos fazer o carregamento pessoalmente. Na escola, falem com um administrador da Incredible e receberá um voucher (ou vários se carregar mais resumos do que o máximo que temos em vouchers). Os vouchers têm um código que se põe no site e o saldo fica automaticamente carregado.</p>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
        
            
            <!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">A equipa</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main cf">
                    <div class="fl">
                        <p>Atualmente, a equipa administradora da Incredible é constituida por 2 pessoas:</p>
                        <ul class="small marged">
                        
                            <li><b>Gonçalo Santos</b> - Fundador, webmaster, revisor</li>
                            <li><b>André Aragão</b> - Co-fundador, escritor principal</li>
                        
                        </ul>
                        <br>
                        <p>Para nos contactar envie um email para: <b>info@<?=HOST?></b></p>
                        <div class="stripe-separator"></div>
                        <p><b>Mas claro que sem vocês, este projeto não seria possível!</b></p>
                    </div>
                    <div class="fr"><img src="/img/logo/logo.png"></div>
                </div>
            </div><!-- End Module -->
            
            <!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">O início da Incredible Community</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
					<p>A Incredible Community foi fundada, no dia 21 de Maio de 2012. A sua ideia principal foi a venda de resumos.<br /><br />O site da Incredible v1 (o que tinha o fundo preto), apesar de ser muito robusto, teve grande sucesso. Logo no dia que abriu, mais de 10 utilizadores criaram a sua conta lá. E ele ainda nem estava acabado! Ainda só dava para ter uma conta, ouvir música que supostamente era para queimar tempo até às férias e ver a contagem decrescente até o início das férias. Mas devido ao seu grande sucesso, pensámos logo em ter uma melhor apresentação, maior rapidez e mais segurança.<br />Foi assim que nasceu a Incredible v2. Agora, o site está melhor desenhado, temos uma página inicial com o calendário de testes, com atualizações feitas pelos administradores e a venda de resumos é totalmente funcional.<br /><br />Obrigado pela vossa ajuda e esperemos que gostem.</p><br><p class="center"><b>Gonçalo Santos</b>, Webmaster</p>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
            
            <div class="round warn-box" id="TOS"><b>Termos e Condições de Serviço</b><br /><ul class="small marged"><li>Apesar de a Incredible Community vender documentos para o uso pessoal do cliente para o ajudar a melhorar as notas, <b>não se responsabiliza</b> por más notas em testes.</li><li>Os documentos vendidos aqui são apenas material de <b>apoio ao estudo</b> logo não substituem a atenção nas aulas e o estudo por parte do aluno.</li><li><b>Não se pode vender/oferecer</b> os resumos vendidos na Incredible Community.</li></ul></div>
           
<?php
showFooter();