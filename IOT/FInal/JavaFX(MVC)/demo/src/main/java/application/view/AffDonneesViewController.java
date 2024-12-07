package application.view;

import java.util.List;
import application.control.AffDonneesController;
import application.model.data.Room;
import application.model.data.RoomManager;
import application.tools.AlertUtilities;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.PieChart;
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
    private ObservableList<String> oListDonnees;  // Liste observable pour les données à afficher
    private ObservableList<Room> roomList = FXCollections.observableArrayList();
    
    public void initContext(Stage _containingStage, AffDonneesController _donnees, RoomManager _roomManager) {
        this.containingStage = _containingStage;
        this.donnees = _donnees;
        this.roomManager = _roomManager;
        this.configure();
    }

    @FXML
    private Tab tabBord;

    @FXML
    private Label labTemp;

    @FXML
    private LineChart<?, ?> lineChart_temp;

    @FXML
    private PieChart pieChartTempe;

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
    private LineChart<String, Number> lineChart_Lum;

    @FXML
    private CategoryAxis TB_xLum;

    @FXML
    private NumberAxis TB_yLum;

    @FXML
    private Label labActivite;

    @FXML
    private ScatterChart<String, Number> scatterChart_Activite;

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
    private ListView<Room> listeSalles;

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
    private ListView<String> tousLesDonnees;

    @FXML
    private Button retour3;

    @FXML
    private void ActionBtnRetour() {
        Stage stage = (Stage) retour1.getScene().getWindow();
        stage.close();
    }

    private void configure() {
        this.containingStage.setOnCloseRequest(e -> this.closeWindow(e));
        this.oListDonnees = FXCollections.observableArrayList(); // Créez une ObservableList pour les données
        tousLesDonnees.setItems(oListDonnees); // Lier la ListView avec cette ObservableList
        this.loadRooms(); // Charge les salles dans l'interface
        this.listeSalles(); 
        this.updateListeSalles(); // Charger les salles dans la ListView
        this.configureListeSalles(); // Configurer l'affichage des salles
        
    }

    // Cette méthode charge les salles dans le MenuButton
    @FXML
    private void afficheSalle(ActionEvent event) {
        listeSalles(); 
    }

    public void loadRooms() {
        if (roomManager != null) {
            List<Room> rooms = roomManager.getRoomsList();
            
            // Vider l'ObservableList avant de la remplir avec les nouvelles données
            oListDonnees.clear();
    
            for (Room room : rooms) {
                // Récupérer la dernière valeur de chaque liste si elle n'est pas vide
                Double lastTemperature = room.getTemperatureList().isEmpty() ? null : room.getTemperatureList().get(room.getTemperatureList().size() - 1);
                Double lastHumidity = room.getHumidityList().isEmpty() ? null : room.getHumidityList().get(room.getHumidityList().size() - 1);
                Double lastCo2 = room.getCo2List().isEmpty() ? null : room.getCo2List().get(room.getCo2List().size() - 1);
                Double lastIllumination = room.getIlluminationList().isEmpty() ? null : room.getIlluminationList().get(room.getIlluminationList().size() - 1);
    
                // Créer une chaîne de caractères avec les dernières données
                String roomData = "Salle: " + room.getRoomId() +
                                  (lastTemperature != null ? ", Temp: " + lastTemperature + "°C" : ", Temp: N/A") +
                                  (lastHumidity != null ? ", Humidité: " + lastHumidity + "%" : ", Humidité: N/A") +
                                  (lastCo2 != null ? ", CO2: " + lastCo2 + "ppm" : ", CO2: N/A") +
                                  (lastIllumination != null ? ", Luminosité: " + lastIllumination + "lx" : ", Luminosité: N/A");
    
                // Ajouter la chaîne de caractères formatée à l'ObservableList
                oListDonnees.add(roomData);
            }
        }
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

    private void updateListeSalles() {
        if (roomManager != null) {
            roomList.clear();
            roomList.addAll(roomManager.getRoomsList()); // Ajouter les salles à l'ObservableList
            listeSalles.setItems(roomList); // Lier l'ObservableList à la ListView
        }
    }

    private void configureListeSalles() {
        listeSalles.setCellFactory(lv -> new javafx.scene.control.ListCell<Room>() {
            @Override
            protected void updateItem(Room item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setText(null);
                } else {
                    setText(item.getRoomId()); // Afficher l'ID de la salle
                }
            }
        });
    }

    
    private void handleRoomSelection(Room room) {
        System.out.println("Salle sélectionnée : " + room.getRoomId());
    
        // Mettre à jour les graphiques pour cette salle
        Double lastTemperature = room.getTemperatureList().isEmpty() ? null : room.getTemperatureList().get(room.getTemperatureList().size() - 1);
        Double lastHumidity = room.getHumidityList().isEmpty() ? null : room.getHumidityList().get(room.getHumidityList().size() - 1);
    
        if (lastTemperature != null) {
            updatePieChart(lastTemperature);
        } else {
            System.out.println("Aucune température disponible pour cette salle.");
        }
    
        if (lastHumidity != null) {
            updateBarChartHum(lastHumidity);
        } else {
            System.out.println("Aucune humidité disponible pour cette salle.");
        }
    
        // Vérifiez et mettez à jour le LineChart pour la luminosité
        if (!room.getIlluminationList().isEmpty()) {
            updateLineChartLum(room);
        } else {
            System.out.println("Aucune luminosité disponible pour cette salle.");
        }
    
        // Mettez à jour le ScatterChart pour le CO2
        updateScatterChartCo2(room);
    }
    
    
    private void updateBarChartHum(Double humidity) {
        // Créer une ObservableList pour afficher l'humidité sur le BarChart
        ObservableList<BarChart.Series> barChartData = FXCollections.observableArrayList();
        
        // Créer une série de données pour l'humidité
        BarChart.Series series = new BarChart.Series();
        series.setName("Humidité");
        
        // Ajouter la donnée de l'humidité dans la série
        series.getData().add(new BarChart.Data("Humidité", humidity));
        
        // Ajouter la série au BarChart
        barChart_Hum.getData().clear(); // Clear any existing data
        barChart_Hum.getData().add(series); // Ajouter la nouvelle série au graphique
    }
    
    private void updatePieChart(Double temperature) {
        // Seuil de température
        double seuilTemperature = 30.0;
    
        // Calcule la part de la température en fonction du seuil
        double temperaturePercentage = Math.min(temperature, seuilTemperature) / seuilTemperature * 100;
    
        // Créer un segment du PieChart pour la température
        ObservableList<PieChart.Data> pieChartData = FXCollections.observableArrayList(
            new PieChart.Data("Température", temperaturePercentage)
        );
    
        // Ajouter un segment pour le reste (si la température est inférieure à 30°C)
        if (temperature < seuilTemperature) {
            pieChartData.add(new PieChart.Data("Ecart par rapport au seuil", 100 - temperaturePercentage));
        } else {
            pieChartData.add(new PieChart.Data("Ecart", 0));
        }
    
        // Mettre à jour le PieChart
        pieChartTempe.setData(pieChartData);
    
        // Ajouter des couleurs (facultatif)
        for (PieChart.Data data : pieChartData) {
            if (data.getName().equals("Température")) {
                data.getNode().setStyle("-fx-pie-color: #ff6347;");  // Couleur de la température
            } else {
                data.getNode().setStyle("-fx-pie-color: #d3d3d3;");  // Couleur du reste
            }
        }
    
        // Afficher la température sur le PieChart
        System.out.println("Température: " + temperature + "°C");
    
        // Afficher la température réelle dans un label ou un autre élément visuel, si nécessaire
        labTemp.setText("Température: " + temperature + "°C");
    }

    private void updateLineChartLum(Room room) {
        // Créer une ObservableList pour contenir les séries de données
        ObservableList<LineChart.Series<String, Number>> lineChartData = FXCollections.observableArrayList();
    
        // Créer une série pour la luminosité
        LineChart.Series<String, Number> series = new LineChart.Series<>();
        series.setName("Luminosité");
    
        // Ajouter les données de luminosité de la salle
        List<Double> illuminationList = room.getIlluminationList();
        for (int i = 0; i < illuminationList.size(); i++) {
            series.getData().add(new LineChart.Data<>("T" + (i + 1), illuminationList.get(i))); // T1, T2, etc. pour le temps
        }
    
        // Ajouter la série au LineChart
        lineChart_Lum.getData().clear();
        lineChart_Lum.getData().add(series);
    
        // Mettre à jour le label de luminosité
        Double lastIllumination = illuminationList.isEmpty() ? null : illuminationList.get(illuminationList.size() - 1);
        if (lastIllumination != null) {
            labLum.setText("Luminosité : " + lastIllumination + " lx");
        } else {
            labLum.setText("Luminosité : N/A");
        }
    }
    
    
    private void updateScatterChartCo2(Room room) {
        // Vérifiez si la liste des valeurs de CO2 n'est pas vide
        if (room.getCo2List().isEmpty()) {
            System.out.println("Aucune donnée de CO2 disponible pour cette salle.");
            return;
        }
    
        // Créez une série pour le graphique
        ScatterChart.Series<String, Number> series = new ScatterChart.Series<>();
        series.setName("CO2");
    
        // Ajoutez les données du CO2 à la série
        List<Double> co2List = room.getCo2List();
        for (int i = 0; i < co2List.size(); i++) {
            series.getData().add(new ScatterChart.Data<>("T" + (i + 1), co2List.get(i))); // T1, T2 pour le temps
        }
    
        // Effacez les anciennes données du ScatterChart
        scatterChart_Activite.getData().clear();
    
        // Ajoutez la nouvelle série au ScatterChart
        scatterChart_Activite.getData().add(series);
    
        // Mettre à jour le label de l'activité si nécessaire
        Double lastCo2 = co2List.get(co2List.size() - 1); // Dernière valeur
        if (lastCo2 != null) {
            labActivite.setText("Activité CO2 : " + lastCo2 + " ppm");
        } else {
            labActivite.setText("Activité CO2 : N/A");
        }
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
