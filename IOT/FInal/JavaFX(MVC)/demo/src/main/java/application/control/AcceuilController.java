package application.control;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import application.App;
import application.model.data.JsonFileWatcher;
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
    private Thread jsonWatcherThread;
    private JsonFileWatcher jsonFileWatcher;

    @Override
    public void start(Stage primaryStage) {
        try {
            // Initialisation de la fenêtre principale
            this.mainStage = primaryStage;

            FXMLLoader loader = new FXMLLoader(getClass().getResource("/ressources/application/view/Accueil.fxml"));
            Scene scene = new Scene(loader.load(), 600, 600);
            primaryStage.setScene(scene);
            primaryStage.setTitle("Accueil");

            AccueilViewController controller = loader.getController();
            controller.initContext(primaryStage, this);
            roomManager = new RoomManager();
            roomManager.loadDataFromJson(); // Charger les données des salles

            // Lancer le watcher JSON
            startJsonWatcher();

            launchPythonScript(); // Démarrer le script Python
            

            primaryStage.show();

            this.mainStage.setOnCloseRequest(e -> {
                e.consume(); // Empêche la fermeture par défaut
                controller.doQuit();
                System.out.println("Arrêt du script Python");
                stopPythonScript(); // Assurez-vous de stopper proprement le script Python
            });
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void startJsonWatcher() {
        jsonFileWatcher = new JsonFileWatcher(RoomManager.getJsonFilePath(), roomManager);
        jsonWatcherThread = new Thread(jsonFileWatcher);
        jsonWatcherThread.setDaemon(true); // S'assurer que le thread ne bloque pas l'arrêt de l'application
        jsonWatcherThread.start();
    }
    private void stopJsonWatcher() {
        if (jsonFileWatcher != null) {
            jsonFileWatcher.stopWatching();
        }
        if (jsonWatcherThread != null && jsonWatcherThread.isAlive()) {
            jsonWatcherThread.interrupt();
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
        AffDonneesController donnees = new AffDonneesController(mainStage, roomManager); // Passer le RoomManager
        donnees.doCapteurDialog();
    }

    public void solar() {
        System.out.println("Solar feature to be implemented.");
    }

    public void config() {
        ConfigController config = new ConfigController(mainStage);
        config.doConfigDialog();
    }

    public void launchPythonScript() {
        pythonThread = new Thread(() -> {
            try {
                System.out.println("Starting Python script...");
                
                
                // Chemin relatif au projet
                String scriptPath = new File("IOT/FInal/mainIOT.py").getCanonicalPath();
                System.out.println("Répertoire courant Java : " + new File(".").getCanonicalPath());
                ProcessBuilder processBuilder = new ProcessBuilder("python", "-u", scriptPath);
                System.out.println("Chemin du script Python : " + scriptPath);

    
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
