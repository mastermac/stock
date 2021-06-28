<nav class="navbar sticky-top navbar-expand-lg navbar navbar-dark bg-dark">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarRightAlignExample" aria-controls="navbarRightAlignExample" aria-expanded="false" aria-label="Toggle navigation">
			<i class="fas fa-bars"></i>
		</button>

		<div class="collapse navbar-collapse" id="navbarRightAlignExample">
			<ul class="navbar-nav justify-content-start mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link" href="#">SilverCity Dashboard</a>
				</li>
			</ul>
			<ul class="navbar-nav ms-auto mb-2 mb-lg-0" id="usernameHeader">
				<li class="nav-item">
					<a class="nav-link" href="#" style="color: white;">Welcome <?php echo strtoupper($_SESSION['username']); ?></a>
				</li>
			</ul>
			<ul class="navbar-nav ms-auto mb-2 mb-lg-0" id="linksHeader">
				<li class="nav-item">
					<a class="nav-link" href="/stock/metal/">Metal</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/stock/stone/">Stone</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/stock/manufacturing/">Manufacturing</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/stock/pack/">Packing List</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/stock/">Stock</a>
				</li>
				<?php if ($_SESSION['usertype'] <= 1)
					echo '<li class="nav-item"><a class="nav-link" href="/stock/po/">PO</a></li>';
				?>
				<li class="nav-item">
					<a class="nav-link" id="logout" href="#" name="logout">Logout</a>
				</li>
				
				<!-- <li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
							Dropdown
						</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item" href="#">Action</a></li>
							<li><a class="dropdown-item" href="#">Another action</a></li>
							<li>
								<hr class="dropdown-divider" />
							</li>
							<li>
								<a class="dropdown-item" href="#">Something else here</a>
							</li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
					</li> -->
			</ul>
		</div>
	</div>
</nav>