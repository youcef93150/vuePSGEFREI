# streamlit_app.py
# Démo minimale pour Streamlit — lit les CSV du dossier `data/` et affiche un tableau + graphique.
# Requirements: streamlit, pandas, plotly

import streamlit as st
import pandas as pd
from pathlib import Path
import plotly.express as px

DATA_DIR = Path(__file__).parent / "data"

st.set_page_config(page_title="Démo PSG - Streamlit", layout="wide")
st.title("Démo PSG — visualisation des données")

if not DATA_DIR.exists():
    st.error(f"Dossier de données introuvable: {DATA_DIR}\nCopie les CSV dans le dossier 'data' à la racine du projet.")
else:
    csv_files = sorted([f.name for f in DATA_DIR.glob("*.csv")])
    if not csv_files:
        st.info("Aucun fichier CSV trouvé dans data/. Place vos fichiers .csv ici (ex: results.csv)")
    else:
        file_choice = st.selectbox("Choisir un fichier CSV", csv_files)
        df = pd.read_csv(DATA_DIR / file_choice)
        st.subheader(f"Aperçu de {file_choice}")
        st.dataframe(df.head(200))

        numeric_cols = df.select_dtypes(include="number").columns.tolist()
        if numeric_cols:
            col = st.selectbox("Colonne numérique pour graphique", numeric_cols)
            st.plotly_chart(px.histogram(df, x=col, nbins=50, title=f"Distribution de {col}"), use_container_width=True)
        else:
            st.info("Aucune colonne numérique détectée pour créer un graphique.")

st.sidebar.header("Instructions")
st.sidebar.markdown(
    "1. Assure-toi que `requirements.txt` contient `streamlit`, `pandas`, `plotly`\n"
    "2. Pousse le repo sur GitHub et crée l'app sur Streamlit Cloud en indiquant `streamlit_app.py` comme Main file path."
)
