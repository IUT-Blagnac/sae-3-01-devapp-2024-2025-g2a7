package application.view;

import java.io.File;
import java.io.FileReader;
import java.util.List;

import application.control.AffDonneesController;
import application.model.data.Room;
import application.model.data.RoomManager;
import application.tools.AlertUtilities;
import javafx.beans.Observable;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.ScatterChart;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.scene.control.Tab;
import javafx.stage.Stage;
import javafx.stage.WindowEvent;

public class AffDonneesViewController {
    // Fenêtre physique ou est la scène contenant le fichier xml contrôlé par this
	private Stage containingStage;
    private AffDonneesController donnees; 
    private RoomManager roomManager;

    /**
	 * Initialisation du contrôleur de vue DailyBankMainFrameController.
	 *
	 * @param _containingStage Stage qui contient le fichier xml contrôlé par
	 *                         DailyBankMainFrameController
	 */
	public void initContext(Stage _containingStage, AffDonneesController _donnees) {
		this.containingStage = _containingStage;
        this.donnees = _donnees; 

	}

    @FXML
    private Tab tabBord;

    @FXML
    private Label labTemp;

    @FXML
    private LineChart<?, ?> lineChart_temp;

    @FXML
    private CategoryAxis TB_xTemp;

    @FXML
    private NumberAxis TB_yTemp;

    @FXML
    private Label labHum;

    @FXML
    private BarChart<?, ?> barChart_Hum;

    @FXML
    private CategoryAxis TB_xHum;

    @FXML
    private NumberAxis TB_yHum;

    @FXML
    private Label labLum;

    @FXML
    private LineChart<?, ?> lineChart_Lum;

    @FXML
    private CategoryAxis TB_xLum;

    @FXML
    private NumberAxis TB_yLum;

    @FXML
    private Label labActivite;

    @FXML
    private ScatterChart<?, ?> scatterChart_Activite;

    @FXML
    private CategoryAxis TB_xActivite;

    @FXML
    private NumberAxis TB_yActivite;

    @FXML
    private MenuButton salles;

    @FXML
    private Button retour1;

    @FXML
    private Tab compDonnees;

    @FXML
    private ListView<?> listeSalles = new ListView();


    @FXML
    private Tab CD_temperature;

    @FXML
    private BarChart<?, ?> barchartTemp;

    @FXML
    private Tab CD_activite;

    @FXML
    private BarChart<?, ?> barchartActivite;

    @FXML
    private Tab CD_luminosite;

    @FXML
    private BarChart<?, ?> barchartLum;

    @FXML
    private Tab CD_humidite;

    @FXML
    private BarChart<?, ?> barchartHum;

    @FXML
    private Button retour2;

    @FXML
    private Tab logs;

    @FXML
    private ListView<?> tousLesDonnees;

    @FXML
    private Button retour3;

    @FXML
    private void ActionBtnRetour() {
        Stage stage = (Stage) retour1.getScene().getWindow();
        stage.close();
    }
    
    private void configure(){
        this.containingStage.setOnCloseRequest(e-> this.closeWindow(e));
        
    }
    // Cette méthode charge les salles dans le MenuButton
    @FXML
    private void afficheSalle(ActionEvent  event){
        listeSalles(); 
    }

    

    private void listeSalles() {
        if (roomManager != null) {
            salles.getItems().clear();  // Efface les anciennes salles si elles existent déjà

            List<Room> rooms = roomManager.getRoomsList();  // Récupère la liste des salles

            for (Room room : rooms) {
                MenuItem item = new MenuItem(room.getRoomId());  // Crée un item de menu pour chaque salle
                item.setOnAction(event -> handleRoomSelection(room));  // Gère la sélection d'une salle
                salles.getItems().add(item);  // Ajoute l'item au MenuButton
            }
        }
    }

    // Méthode appelée lorsqu'une salle est sélectionnée
    private void handleRoomSelection(Room room) {
        System.out.println("Salle sélectionnée : " + room.getRoomId());
        // Vous pouvez ici ajouter une logique pour afficher les données de la salle dans l'interface
    }

    
    
    /**
	 * Action menu quitter. Demander une confirmation puis fermer la fenêtre (donc
	 * l'application car fenêtre principale).
	 */
	@FXML
	private void doQuit() {
		if (AlertUtilities.confirmYesCancel(this.containingStage, "Quitter l'application",
				"Etes vous sur de vouloir quitter l'appli ?", null, AlertType.CONFIRMATION)) {
			this.containingStage.close();
		}
	}
    // Gestion du stage
    private Object closeWindow(WindowEvent e) {
        this.doQuit();
        e.consume();
        return null;
    }


    public void showDialog() {
        this.containingStage.showAndWait();
    }

}
