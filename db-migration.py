import sqlite3
import pandas as pd

conn = sqlite3.connect('interpost_copie')

sql_query = pd.read_sql_query ('''
                               SELECT
                               *
                               FROM users
                               ''', conn)

df = pd.DataFrame(sql_query)
print (df)
