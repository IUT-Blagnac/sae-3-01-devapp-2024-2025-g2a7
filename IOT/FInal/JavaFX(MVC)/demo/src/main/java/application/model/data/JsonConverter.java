package application.model.data;

import java.io.File;
import java.io.IOException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.core.type.TypeReference;
import java.util.Map;

public class JsonConverter {

    /**
     * Charge les données énergétiques depuis un fichier JSON et les convertit en un objet  EnergieData.
     *
     * @return Un objet {EnergieData} contenant les enregistrements d'énergie, ou null en cas d'erreur de lecture.
     */
    public EnergieData loadEnergieDataFromJson() {
        try {
            ObjectMapper objectMapper = new ObjectMapper();

            Map<String, Object> solarData = objectMapper.readValue(
                new File("IOT/Final/datas/Solaredge_filtre_data.json"),
                new TypeReference<Map<String, Object>>() {}
            );

            EnergieData energieData = new EnergieData();

            Map<String, Object> solarEntries = (Map<String, Object>) solarData.get("solar");

            for (Map.Entry<String, Object> entry : solarEntries.entrySet()) {
                String index = entry.getKey();
                Map<String, Object> solarRecord = (Map<String, Object>) entry.getValue();

                EnergieData.EnergieRecord record = new EnergieData.EnergieRecord();

                // Utilisation de `get("energy")` pour accéder à la valeur d'énergie et s'assurer qu'elle est un nombre Double
                record.setEnergie_totale(getEnergyValue(solarRecord, "lifeTimeData"));
                record.setEnergie_annee_derniere(getEnergyValue(solarRecord, "lastYearData"));
                record.setEnergie_mois_dernier(getEnergyValue(solarRecord, "lastMonthData"));
                record.setEnergie_jour_dernier(getEnergyValue(solarRecord, "lastDayData"));
                record.setLastUpdateTime((String) solarRecord.get("lastUpdateTime"));
                record.setPuissance(getPuissance(solarRecord, "currentPower"));

                // Ajouter l'enregistrement dans l'objet EnergieData
                energieData.addRecord(index, record);
            }

            return energieData;

        } catch (IOException e) {
            System.err.println("Erreur lors de la lecture du fichier JSON: " + e.getMessage());
            return null;
        }
    }

    /**
     * Récupère la valeur de la puissance à partir des données de l'enregistrement solaire.
     * 
     * @param solarRecord L'enregistrement contenant les données de puissance.
     * @param key La clé correspondant à la puissance dans les données (ex. "currentPower").
     * @return La valeur de la puissance, ou 0.0 si la valeur n'est pas valide.
     */
    private Double getPuissance(Map<String, Object> solarRecord, String key) {
        Map<String, Object> data = (Map<String, Object>) solarRecord.get(key);
        if (data != null) {
            Object power = data.get("power");
            if (power instanceof Double) {
                return (Double) power;
            } else {
                System.err.println("Valeur de puissance invalide pour " + key);
            }
        }
        return 0.0; 
    }

    /**
     * Méthode utilitaire pour récupérer la valeur d'énergie d'un sous-objet dans les données solaires.
     * 
     * @param solarRecord L'enregistrement contenant les données d'énergie.
     * @param key La clé correspondant à l'énergie (ex. "lifeTimeData", "lastYearData", etc.).
     * @return La valeur de l'énergie, ou 0.0 si la valeur n'est pas valide.
     */
    private Double getEnergyValue(Map<String, Object> solarRecord, String key) {
        Map<String, Object> data = (Map<String, Object>) solarRecord.get(key);
        if (data != null) {
            Object energy = data.get("energy");
            if (energy instanceof Double) {
                return (Double) energy;
            } else {
                System.err.println("Valeur d'énergie invalide pour " + key);
            }
        }
        return 0.0; // Retourne 0 si aucune valeur valide n'est trouvée
    }
}
