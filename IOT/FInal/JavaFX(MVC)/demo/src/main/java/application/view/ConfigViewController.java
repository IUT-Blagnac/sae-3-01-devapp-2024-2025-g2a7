package application.view;

import application.control.AffDonneesController;
import application.control.ConfigController;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Slider;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

public class ConfigViewController {
    private Stage containingStage;
    private ConfigController config; 

    /**
	 * Initialisation du contrôleur de vue DailyBankMainFrameController.
	 *
	 * @param _containingStage Stage qui contient le fichier xml contrôlé par
	 *                         DailyBankMainFrameController
	 */
	public void initContext(Stage _containingStage, ConfigController _config) {
		this.containingStage = _containingStage;
        this.config = _config; 

	}
    @FXML
    private TextField outField;

    @FXML
    private Slider timeSlider;

    @FXML
    private TextField activityMin;

    @FXML
    private TextField activityMax;

    @FXML
    private TextField humidityMin;

    @FXML
    private TextField humidityMax;

    @FXML
    private TextField coMin;

    @FXML
    private TextField coMax;

    @FXML
    private TextField illuminMin;

    @FXML
    private TextField illuminMax;

    @FXML
    private Button ModifOut;

    @FXML
    private Button cancel;

    @FXML
    private Button valid;

    @FXML
    private Button retour2;

    @FXML
    private void modifOutField() {
        // Logique pour modifier le champ outField
    }

    @FXML
    private void cancel() {
        // Logique pour annuler les modifications
    }

    @FXML
    private void valid() {
        // Logique pour valider les modifications
    }

    @FXML
    private void handleBackButtonAction() {
        // Fermer la fenêtre actuelle
        Stage stage = (Stage) retour2.getScene().getWindow();
        stage.close();
    }

    public void showDialog() {
        this.containingStage.showAndWait();
    }
    
}