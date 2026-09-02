<div class="topPart">
	<div class="gradient1">
		<div class="gradient2">
			<header>
				<H2 id="headerh2"><a id="headerName" class="" href="/">FantasyRealm Online</a> </H2>
				<div>
					<nav>
						<?php add_link_menu('/', 'Home') ?>
						<span class="space">|</span>
						<?php add_link_menu('/menu', 'Menu') ?>
						<span class="space">|</span>
						<?php
						if(is_connected())
							echo"<a href='/logout'>Se deconnecter</a>";
						else
							add_link_menu('/login', 'Se connecter');
						?>
					</nav>

					<a href="/menu/mon-espace"><span class="profileCase"><img class="profilePic" src="/assets/frontend/SVG/Icones/Head.svg" alt="" width="39" height="39"></span></a>
				</div>
			</header>

			<?php
				if(defined('PAGE_IS_HOME') && PAGE_IS_HOME):
			?>
					<img class="logo" src="/assets/frontend/SVG/Logo/Print_Transparent.svg" alt="Logo" width="907" height="726">
			<?php
				endif;
			?>
		</div>
	</div>
</div>