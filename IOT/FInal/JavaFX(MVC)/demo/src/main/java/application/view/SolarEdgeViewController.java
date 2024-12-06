package application.view;

import java.io.IOException;

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
    private EnergieData energieData;
    private Number lastEnergieJourDernier = null;
    private Number lastEnergieMoisDernier = null;
    private Number lastEnergieTotale = null;


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
        
        JsonConverter jsonConverter = new JsonConverter();
        energieData = jsonConverter.loadEnergieDataFromJson();
    
        startUpdateThreadForEvolution();
        startUpdateThreadForComparaison();
        startUpdateThreadForTotal();
    }
    

   
    
    

    private void startUpdateThreadForEvolution() {
        Thread threadEvolution = new Thread(() -> {
            while (true) { 
                try {
                    Thread.sleep(10000); 
                    Platform.runLater(this::updateGrapheEvolution);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                    Thread.currentThread().interrupt();
                }
            }
        });
        threadEvolution.start();
    }

    private void startUpdateThreadForComparaison() {
        Thread threadComparaison = new Thread(() -> {
            while (true) {
                try {
                    Thread.sleep(10000); 
                    Platform.runLater(this::updateGrapheComparaison);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                    Thread.currentThread().interrupt();
                }
            }
        });
        threadComparaison.start();
    }

    private void startUpdateThreadForTotal() {
        Thread threadTotal = new Thread(() -> {
            while (true) {
                try {
                    Thread.sleep(10000); 
                    Platform.runLater(this::updateGrapheTotal);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                    Thread.currentThread().interrupt();
                }
            }
        });
        threadTotal.start();
    }

    private void updateGrapheEvolution() {
        if (energieData == null) return;
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Énergie quotidienne");

        series.getData().add(new XYChart.Data<>("Jour Dernier", energieData.getEnergie_jour_dernier()));

        GrapheEvolution.getData().clear();
        GrapheEvolution.getData().add(series);
    }

    private void updateGrapheComparaison() {
        if (energieData == null) return;
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Comparaison");

        series.getData().add(new XYChart.Data<>("Mois dernier", energieData.getEnergie_mois_dernier()));
        series.getData().add(new XYChart.Data<>("Moyenne mensuelle", energieData.getEnergie_moyenne_mensuelle()));


        GrapheComparaison.getData().clear();
        GrapheComparaison.getData().add(series);
    }

    private void updateGrapheTotal() {
        if (energieData == null) return;
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Total énergie");

        series.getData().add(new XYChart.Data<>("Energie totale", energieData.getEnergie_totale()));
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
