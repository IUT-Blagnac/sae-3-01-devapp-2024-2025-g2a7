package application.view;

import java.io.IOException;
import java.util.Map;
import application.control.SolarEdgeController;
import application.model.data.EnergieData;
import application.model.data.JsonConverter;
import javafx.application.Platform;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Button;
import javafx.stage.Stage;

public class SolarEdgeViewController {

    private Stage containingStage;
    private SolarEdgeController solar;
    private EnergieData energieData; // Une seule instance de EnergieData contenant les enregistrements

    @FXML
    private Button btnRetour;
    @FXML
    private Button btnRetour1;
    @FXML
    private Button btnRetour2;
    @FXML
    private LineChart<String, Number> GrapheEvolution;
    @FXML
    private BarChart<String, Number> GrapheComparaison;
    @FXML
    private LineChart<String, Number> GrapheTotal;

    @FXML
    private CategoryAxis xAxisEnergyEvolution;
    @FXML
    private NumberAxis yAxisEnergyEvolution;

    @FXML
    private CategoryAxis xAxisEnergyComparison;
    @FXML
    private NumberAxis yAxisEnergyComparison;

    @FXML
    private CategoryAxis xAxisTotalEnergy;
    @FXML
    private NumberAxis yAxisTotalEnergy;

    public void initContext(Stage _containingStage, SolarEdgeController _solar) {
        this.containingStage = _containingStage;
        this.solar = _solar;
    }

    @FXML
    public void initialize() {
        // Charger les données à partir du JSON
        JsonConverter jsonConverter = new JsonConverter();
        energieData = jsonConverter.loadEnergieDataFromJson();

        // Mettre à jour les graphiques directement lors de l'initialisation
        updateGrapheEvolution();
        updateGrapheComparaison();
        updateGrapheTotal();
    }

    // Mise à jour du graphique d'évolution (jour dernier)
    private void updateGrapheEvolution() {
        if (energieData == null || energieData.getRecords().isEmpty()) return;

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Énergie quotidienne");

        // Ajouter chaque donnée d'énergie (jour dernier) dans la série
        for (Map.Entry<String, EnergieData.EnergieRecord> entry : energieData.getRecords().entrySet()) {
            EnergieData.EnergieRecord record = entry.getValue();
            series.getData().add(new XYChart.Data<>(record.getLastUpdateTime(), record.getEnergie_jour_dernier()));
        }

        GrapheEvolution.getData().clear();
        GrapheEvolution.getData().add(series);
    }

    // Mise à jour du graphique de comparaison (mois dernier et moyenne mensuelle)
    private void updateGrapheComparaison() {
    // Vérifier si les données sont valides (non nulles et non vides)
    if (energieData == null || energieData.getRecords().isEmpty()) return;

    // Obtenez le dernier enregistrement (ici, on suppose que l'index du dernier élément est le plus grand)
    Map.Entry<String, EnergieData.EnergieRecord> lastEntry = null;
    for (Map.Entry<String, EnergieData.EnergieRecord> entry : energieData.getRecords().entrySet()) {
        lastEntry = entry; // Récupère le dernier élément
    }

    // Si aucun enregistrement n'est trouvé, on ne fait rien
    if (lastEntry == null) return;

    // Récupérer les données du dernier enregistrement
    String index = lastEntry.getKey();
    EnergieData.EnergieRecord lastRecord = lastEntry.getValue();

    // Créer une nouvelle série pour afficher les comparaisons
    XYChart.Series<String, Number> series = new XYChart.Series<>();
    series.setName("Comparaison Dernier Prélèvement");

    // Ajouter les données du dernier enregistrement à la série (uniquement pour "mois dernier" et "moyenne mensuelle")
    series.getData().add(new XYChart.Data<>(  " Mois dernier", lastRecord.getEnergie_mois_dernier()));
    series.getData().add(new XYChart.Data<>( " Moyenne mensuelle", lastRecord.getEnergie_moyenne_mensuelle()));

    // Effacer les anciennes données et ajouter la nouvelle série
    GrapheComparaison.getData().clear();
    GrapheComparaison.getData().add(series);
}


    // Mise à jour du graphique de l'énergie totale
    private void updateGrapheTotal() {
        if (energieData == null || energieData.getRecords().isEmpty()) return;

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Total énergie");

        // Ajouter chaque donnée d'énergie (énergie totale) dans la série
        for (Map.Entry<String, EnergieData.EnergieRecord> entry : energieData.getRecords().entrySet()) {
            EnergieData.EnergieRecord record = entry.getValue();
            series.getData().add(new XYChart.Data<>(record.getLastUpdateTime(), record.getEnergie_totale()));
        }

        GrapheTotal.getData().clear();
        GrapheTotal.getData().add(series);
    }

    @FXML
    private void ActionBtnRetour(ActionEvent event) {
        Button sourceButton = (Button) event.getSource();
        Stage stage = (Stage) sourceButton.getScene().getWindow();
        stage.close();
    }

    public void showDialog() {
        this.containingStage.showAndWait();
    }
}
