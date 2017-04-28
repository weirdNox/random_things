<?php
require_once '../php/Membership.php';
$membership = new Membership();
$info = $membership->verifyInfo();
?>
<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
<head>
    <!--Stylesheets-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/reset.css" type="text/css" />
    <link rel="stylesheet" href="/css/reset.css" id="theme-style" type="text/css" />

    <!-- Meta Info -->
    <title>Incredible Community</title>
    <meta charset="UTF-8">
    <meta name="description" content="Website que fornece resumos de todas as disciplinas escolares do 3º Ciclo.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Open Graph Protocol -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Incredible Community">
    <meta property="og:url" content="http://incredible.ihostwell.com/login.php">
    <meta property="og:image" content="http://incredible.ihostwell.com/img/logo/logo-web.png">
    <meta property="og:description" content="Website que fornece resumos de todas as disciplinas escolares do 3º Ciclo.">
    
    <!-- Javascript -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/js/js.js?5"></script>
    <script>verifyTheme(false);</script>
    
    <!--Stylesheets-->
    <link rel="stylesheet" id="style" href="/css/default.css?3" type="text/css" />
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(["_setAccount", "UA-35035660-1"]);
        _gaq.push(["_trackPageview"]);
        
        (function() {
        var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
        ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
</head>
	
<body>
<!-- Top Bar -->
<div id="top-bar">
    <div class="container-padding cf">
    	<!-- Nav -->
        <ul id="nav" class="fl">
            <li><a class="button round image-left dark ic-menu-user">Logged in as</a>
                <ul>
                    <li><a href="#">Profile</a></li>
                    <li><a href="#">Settings</a></li>
                    <li><a href="/login.php">Logout</a></li>
                </ul>
            </li>
            <li><a href="/login.php" class="button round image-left dark ic-menu-logout">Log Out</a></li>
        </ul> <!-- End Nav -->
        
        <div class="fr white-text" style="margin-top: 0.3125em;">Yey?</div>
    </div>
</div> <!-- End Top Bar -->


<!-- Tabs -->
<div id="header-with-tabs">
    <div class="container-padding cf">
        <ul id="tabs">
            <li><a href="/index.php" class="ic-tab-grid image-left">Início</a></li>
        </ul>
    </div>
</div> <!-- End Tabs -->


<!-- Content -->
<div id="content">
    <div class="container-padding cf"><!-- Padding -->
    	<!-- Side Menu -->
        <div class="side-menu fl">
            <h3>Links úteis</h3>
            <ul>
                <li><a href="http://www.facebook.com/pages/Incredible-Community/328267827261868" target="_blank">Facebook Page</a></li>
                <li><a href="http://www.facebook.com/groups/171046736359090/" target="_blank">Facebook Group</a></li>
                <li><a href="http://www.minecraft.net/" target="_blank">Minecraft (WTF??)</a></li>
            </ul>
        </div> <!-- End Side Menu -->
        
        <!-- Main Content -->
        <div id="main-content" class="fr">
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Tables</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
                <table class="accounts">
                
                    <thead>
                
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    
                    </thead>

                    <tfoot>
                    
                        <tr>
                        
                            <td colspan="5" class="table-footer">
                            
                                <label for="table-select-actions">With selected:</label>

                                <select id="table-select-actions">
                                    <option value="option1">Edit</option>
                                    <option value="option2">Deactivate</option>
                                    <option value="option3">Delete</option>
                                </select>
                                
                                <a href="#" class="round button blue text-upper small-button">Apply to selected</a>	

                            </td>
                            
                        </tr>
                    
                    </tfoot>
                    
                    <tbody>

                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Gonçalo Santos</td>
                            <td>iNoX</td>
                            <td><a href="#">goncalossantos98@gmail.com</a></td>
                            <td>
                                <a href="#" class="table-actions-button ic-table-edit"></a>
                                <a href="#" class="table-actions-button ic-table-delete"></a>
                            </td>
                        </tr>
                        
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Gonçalo Santos</td>
                            <td>iNoX</td>
                            <td><a href="#">goncalossantos98@gmail.com</a></td>
                            <td>
                                <a href="#" class="table-actions-button ic-table-edit"></a>
                                <a href="#" class="table-actions-button ic-table-delete"></a>
                            </td>
                        </tr>
                        
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Gonçalo Santos</td>
                            <td>iNoX</td>
                            <td><a href="#">goncalossantos98@gmail.com</a></td>
                            <td>
                                <a href="#" class="table-actions-button ic-table-edit"></a>
                                <a href="#" class="table-actions-button ic-table-delete"></a>
                            </td>
                        </tr>
                        
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>Gonçalo Santos</td>
                            <td>iNoX</td>
                            <td><a href="#">goncalossantos98@gmail.com</a></td>
                            <td>
                                <a href="#" class="table-actions-button ic-table-edit"></a>
                                <a href="#" class="table-actions-button ic-table-delete"></a>
                            </td>
                        </tr>
                    
                    </tbody>
                    
                </table>
            
                </div><!-- End Module Main -->
            </div><!-- End Module -->
            
            <!-- Module -->
            <div class="content-module">
            	<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Forms</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main cf">
            
                    <div class="half-size-column fl">
						
							<form action="#">
							
								<fieldset>
								
									<p>
										<label for="simple-input">Simple input</label>
										<input type="text" id="simple-input" class="round default-width-input" />
									</p>
									
									<p>
										<label for="full-width-input">A full width input</label>
										<input type="text" id="full-width-input" class="round full-width-input"/>
										<em>This is a full width input. It will take 100% of the available width.</em>								
									</p>
	
									<p>
										<label for="another-simple-input">Text input with additional description</label>
										<input type="text" id="another-simple-input" class="round default-width-input"/>
										<em>You can add a hint or a small description here.</em>								
									</p>
	
									<p class="form-error">
										<label for="error-input">Error text input</label>
										<input type="text" id="error-input" class="round default-width-input error-input"/>
										<em>This is an optional error description that can be associated with an input. You see?? It's really cool... I should put here some Lorem Ipsum :)</em>								
									</p>
									
								</fieldset>
							
							</form>
						
						</div> <!-- end half-size-column -->
						
						<div class="half-size-column fr">
						
							<form action="#">
							
								<fieldset>
								
									<p>
										<label for="textarea">Textarea</label>
										<textarea id="textarea" class="round full-width-textarea" rows="10"></textarea>
									</p>
									
									<div class="stripe-separator"><!--  --></div>
	
									<p>
										<label>Checkboxes</label>
										<label for="selected-checkbox" class="alt-label"><input type="checkbox" id="selected-checkbox" checked="checked" />A selected checkbox</label>
										<label for="unselected-checkbox" class="alt-label"><input type="checkbox" id="unselected-checkbox" />An uselected checkbox</label>
									</p>
	
									<p>
										<label>Radio buttons</label>
										<label for="selected-radio" class="alt-label"><input type="radio" id="selected-radio" checked="checked" name="test" />A selected radio</label>
										<label for="unselected-radio" class="alt-label"><input type="radio" id="unselected-radio" name="test" />An uselected radio</label>
									</p>
	
									<p class="form-error-input">
										<label for="dropdown-actions">Dropdown</label>
	
										<select id="dropdown-actions">
											<option value="option1">Select your action here</option>
										</select>
									</p>
	
									<div class="stripe-separator"><!-- SEPARATOR --></div>
	
									<input type="submit" value="Submit Button" class="button round green image-right ic-arrow-right" />
									
								</fieldset>
							
							</form>
							
						</div> <!-- end half-size-column -->
            
                </div><!-- End Module Main -->
            </div> <!-- End Module -->
            
            <!-- Half-Size -->
            <div class="half-size-column fl">
            	<!-- Module -->
                <div class="content-module">
                	<!-- Module Heading -->
                    <div class="content-module-heading cf">
                        <h3 class="fl">Half Size :D</h3>
                        <span class="fr span-text">Clica para contrair</span>
                        <span class="fr expand-span span-text">Clica para expandir</span>
					</div><!-- End Module Heading -->
                
                	<!-- Module Main -->
                    <div class="content-module-main">
                        <div class="info-box round">This is an information box. It will resize based on it’s contents.</div>
                        <div class="confirm-box round">This is fully functional! Look: <br/> Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi.</div>
                        <div class="err-box round">This is an error box. It will resize based on it’s contents.</div>
                        <div class="warn-box round">This is an warning box. It will resize based on it’s contents.</div>
                    </div><!-- End Module Main -->
            	</div><!-- End Module -->
            </div><!-- End Half-Size -->
            
            <!-- Half-Size -->
            <div class="half-size-column fr">
            	<!-- Module -->
                <div class="content-module">
                	<!-- Module Heading -->
                    <div class="content-module-heading cf">
                        <h3 class="fl">Another Half Size :D</h3>
                        <span class="fr span-text">Clica para contrair</span>
                    	<span class="fr expand-span span-text">Clica para expandir</span>
                    </div><!-- End Module Heading -->
                
                	<!-- Module Main -->
                    <div class="content-module-main">
                        <div class="info-box round">YEY!</div>
                    </div><!-- End Module Main -->
                </div><!-- End Module -->
            </div><!-- End Half-Size -->
        </div> <!-- End Main Content -->
    </div><!-- End Padding -->
</div> <!-- End content -->


<!-- Footer -->
<div id="footer">
    <p class="text-upper">&copy; Copyright 2012 <strong><a href="http://www.facebook.com/pages/Incredible-Community/328267827261868" class="dark-link">Incredible Community.</a></strong></p>
    <p>Created by <strong><a href="http://www.facebook.com/GoncaloSSantos" target="_blank" class="dark-link">iNoX</a></strong>.</p>
</div> <!-- End Footer -->
</body>
</html>