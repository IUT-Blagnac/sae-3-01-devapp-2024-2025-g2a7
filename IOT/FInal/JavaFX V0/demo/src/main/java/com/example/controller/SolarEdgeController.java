package com.example.controller;

import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Button;
import javafx.stage.Stage;

public class SolarEdgeController {

    
    @FXML
    private Button btnRetour;
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

    
    @FXML
    public void initialize() {
        
        updateGrapheEvolution();
        updateGrapheComparaison();
        updateGrapheTotal();
    }

    
    private void updateGrapheEvolution() {
        
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Énergie quotidienne");

        // Données test
        series.getData().add(new XYChart.Data<>("2024-11-13", 2719));
        series.getData().add(new XYChart.Data<>("2024-11-14", 159));
        series.getData().add(new XYChart.Data<>("2024-11-15", 1500));
        series.getData().add(new XYChart.Data<>("2024-11-16", 2000));

        series.getData().add(new XYChart.Data<>("2024-11-17", 1800));


        
        GrapheEvolution.getData().clear();
        GrapheEvolution.getData().add(series);
    }

    
    private void updateGrapheComparaison() {
        
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Comparaison");

        // Données test
        series.getData().add(new XYChart.Data<>("Mois dernier", 128917)); 
        series.getData().add(new XYChart.Data<>("Moyenne annuelle", (3244173 - 2763789) / 12)); 

        
        GrapheComparaison.getData().clear();
        GrapheComparaison.getData().add(series);
    }

    
    private void updateGrapheTotal() {
        
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Total énergie");

        // Données test
        series.getData().add(new XYChart.Data<>("2024-11-13", 3244173));
        series.getData().add(new XYChart.Data<>("2024-11-14", 3250385));
        series.getData().add(new XYChart.Data<>("2024-11-15", 3252105));

        GrapheTotal.getData().clear();
        GrapheTotal.getData().add(series);
        
    }

    
    @FXML
    private void ActionBtnRetour() {
        Stage stage = (Stage) btnRetour.getScene().getWindow();
        stage.close();
    }
}
