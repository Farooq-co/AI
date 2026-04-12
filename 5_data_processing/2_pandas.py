import pandas as pd
# data= [1, 2, 3, 4, 5]
# series = pd.Series(data,index=['a', 'b', 'c', 'd', 'e'])
# print(series['c'])  # Accessing element by index label
data= {'Name': ['Alice', 'Bob', 'Charlie', 'David'],
       'Age': [25, 30, 35, 40],
        'City': ['New York', 'Los Angeles', 'Chicago', 'Houston']}
df = pd.DataFrame(data)
print(df)
# df=pd.read_csv('data.csv')
# print(df)
df.to_csv('output.csv', index=False)
df.to_json('output.json')