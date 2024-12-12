<!-- Sidebar -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body overflow-auto">
    <ul class="sidebar-nav list-unstyled">
        <?php
          include ("connect.inc.php");
          $reqCateg = $conn->prepare("SELECT * FROM Categorie WHERE idCatPere IS NULL;") ;
          $reqCateg->execute();
          foreach($reqCateg as $categ) {
            echo "<li class=\"sidebar-item\">";
              echo "<a href=\"#\" class=\"sidebar-link has-dropdown collapsed\" data-bs-toggle=\"collapse\" data-bs-target=\"#".$categ['nomCat']."\" aria-expanded=\"false\" aria-controls=\"".$categ['nomCat']."\">".$categ['nomCat']."</a>";
              echo "<ul id=\"".$categ['nomcat']."\" class=\"sidebar-dropdown list-unstyled collapse\">";
              $reqSousCateg = $conn->prepare("SELECT * FROM Categorie WHERE idCatPere = ? ;") ;
              $reqSousCateg->execute([$categ['idCat']]);
              foreach($reqSousCateg as $sousCateg) {
                echo "<li class=\"sidebar-item\"><a href=\"#\" class=\"sidebar-link\">".$sousCateg['nomCat']."</a></li>";
              }
              $reqSousCateg->closeCursor();
              echo "</ul>";
            echo "</li>";
          }   
          $reqCateg->closeCursor();
        ?>
    </ul>
  </div>
</div>