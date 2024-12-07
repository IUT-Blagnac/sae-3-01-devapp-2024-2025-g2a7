package application.control;

import java.io.IOException;

import application.App;
import application.view.AccueilViewController;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class AcceuilController extends Application {
    private Stage mainStage; 
    private static Scene scene;

    @Override
    public void start(Stage primaryStage) {
        try {
            // Initialisation de la fenêtre principale
            this.mainStage = primaryStage; // Ajouter cette ligne

            FXMLLoader loader = new FXMLLoader(getClass().getResource("/ressources/application/view/Accueil.fxml"));
            Scene scene = new Scene(loader.load(), 600, 600);
            primaryStage.setScene(scene);
            primaryStage.setTitle("Accueil");

            AccueilViewController controller = loader.getController();
            controller.initContext(primaryStage, this);

            primaryStage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private static javafx.scene.Parent loadFXML(String fxml) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(AcceuilController.class.getResource(fxml + ".fxml"));
        return fxmlLoader.load();
    }

    public static void setRoot(String fxml) throws IOException {
        scene.setRoot(loadFXML(fxml));
    }

    public static void run() {
        launch(); // Lancer l'application JavaFX
    }

    public void affDonnees() {
        AffDonneesController donnees = new AffDonneesController(mainStage);
        donnees.doCapteurDialog();
    }

    public void solar() {
        SolarEdgeController solar = new SolarEdgeController (mainStage);
        solar.doCapteurDialog();
    }

    public void config() {
        ConfigController config = new ConfigController(mainStage);
        config.doConfigDialog();
    }
}
