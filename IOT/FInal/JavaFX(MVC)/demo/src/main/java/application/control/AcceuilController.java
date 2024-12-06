package application.control;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import application.App;
import application.model.data.RoomManager;
import application.view.AccueilViewController;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class AcceuilController extends Application {
    private Stage mainStage; 
    private static Scene scene;
    private Thread pythonThread;
    private Process pythonProcess;
    private RoomManager roomManager;

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
            roomManager = new RoomManager();
            roomManager.loadDataFromJson();

            launchPythonScript(); 

            this.mainStage.setOnCloseRequest(e -> {
            System.out.println("Arret du script Python");
            stopPythonScript();
            });
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
        System.out.println("Solar feature to be implemented.");
    }

    public void config() {
        ConfigController config = new ConfigController(mainStage);
        config.doConfigDialog();
    }

    public void launchPythonScript(){
        pythonThread = new Thread(() -> {
            try {
                System.out.println("Starting Python script...");
    
                String scriptPath = "C:\\Users\\Etudiant\\Downloads\\sae-3-01-devapp-2024-2025-g2a7\\IOT\\FInal\\mainIOT.py";
                ProcessBuilder processBuilder = new ProcessBuilder("python", "-u", scriptPath);
                File scriptDirectory = new File(scriptPath).getParentFile();
                processBuilder.directory(scriptDirectory);
    
                processBuilder.environment().put("PYTHONENCODING", "UTF-8");
                processBuilder.redirectErrorStream(true);
    
                this.pythonProcess = processBuilder.start();
                
                try (InputStream inputStream = pythonProcess.getInputStream();
                     BufferedReader reader = new BufferedReader(new InputStreamReader(inputStream))) {
                    String line;
                    while ((line = reader.readLine()) != null) {
                        System.out.println(line);
                    }
                }
    
                int exitCode = pythonProcess.waitFor();
                System.out.println("Python script exited with code: " + exitCode);
    
                System.out.println("Python script finished.");
            } catch (Exception e) {
                if (pythonProcess != null && pythonThread != null) {
                    pythonProcess.destroy();
                    pythonThread.interrupt();
                }
                e.printStackTrace();
            }
        });
    
        pythonThread.start();
    }
    
    public void stopPythonScript(){
        if (pythonProcess != null && pythonThread.isAlive()) {
            pythonProcess.destroy();
        }
        if (pythonThread != null && pythonThread.isAlive()) {
            pythonThread.interrupt();
        }
    }
}
