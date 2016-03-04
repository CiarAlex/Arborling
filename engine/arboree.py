"""
# arboree.py: 
#    
# Author:
#    lvanni@unice.fr
#
    This file is part of Arborling.

    Arborling is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Arborling is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Arborling.  If not, see <http://www.gnu.org/licenses/>.
"""
import math
from scipy.stats import chi2_contingency
import sys
import json


SEUIL_MAX = 11

"""
COOR CALCUL
"""
SIZE_X = 1000 # px
SIZE_Y = 1000 # px

MAX_LINE_SIZE = 200

"""
"""
class Node:
    def __init__(self, id):
        self.id = id;
        self.x = SIZE_X/2;
        self.y = SIZE_Y/2;
        self.fils = {};
        self.distances = {};

"""
"""
def print_tree(tree):
    print tree.id, tree.x, tree.y
    for id, distance in tree.distances.iteritems():
        print distance, " -->"
        print_tree(tree.fils[id])
   
"""
""" 
def nb_feuille(tree):
    if tree.fils == {}:
        return 1
    else:
        cpt = 0
        for node in tree.fils.values():
            cpt += nb_feuille(node)
        return cpt

"""
"""
def setCoordinate(tree, alpha, teta):
    for id, node in tree.fils.iteritems():
        nf = nb_feuille(node);
        distance = tree.distances[id] + MAX_LINE_SIZE
        
        #int Xb = (int) Math.round(root.getX() + distance * Math.cos(Math.toRadians(alpha + ((nbFeuille * teta) / 2))));
        #int Yb = (int) Math.round(root.getY() + distance * Math.sin(Math.toRadians(alpha + ((nbFeuille * teta) / 2))));;
        
        node.x = round(tree.x + distance * math.cos(math.radians(alpha + ((nf*teta) / 2))))
        #print tree.x, distance, nf, (alpha + ((nf*teta) / 2)), math.radians(alpha + ((nf*teta) / 2)), math.cos(math.radians(alpha + ((nf*teta) / 2))), node.x  
        node.y = round(tree.y + distance * math.sin(math.radians(alpha + ((nf*teta) / 2))))
        
        setCoordinate(node, alpha, teta);
        alpha += nf * teta;
        
"""
Calcul coord for each node
"""
def calcul_coord(tree):
    nodes = {}
    
    root_id = 0
    for father_id in tree.keys():
        for son_id in tree[father_id].keys():
            root_id += 1
            father_node = nodes.get(father_id, Node(father_id))
            son_node = nodes.get(son_id, Node(son_id))
            father_node.fils[son_id] = son_node
            father_node.distances[son_id] = tree[father_id][son_id]
            
            nodes[father_id] = father_node
            nodes[son_id] = son_node
            
    nf = nb_feuille(nodes[root_id])
    
    setCoordinate(nodes[root_id], 0, 360 / nf);
    
    return nodes, root_id
    
"""
Return Ki2 matrix
"""       
def calcul_khi2(distance_matrix): 
    
    result = {}

    for key1 in distance_matrix.keys():
        result[key1] = {}
        for key2 in distance_matrix.keys():
            if key1 == key2:
                result[key1][key2] = 0
            else:
                result[key1][key2], p, dof, ex = chi2_contingency([distance_matrix[key1].values(), distance_matrix[key2].values()])
    
    return result

"""
Return True is dist(ab) is the shortest
"""
def compare_abcd(dist_matrix, a, b, c ,d , strict):
    if strict:
        return dist_matrix[a][b] < dist_matrix[a][c] and dist_matrix[a][b] < dist_matrix[a][d] and dist_matrix[a][b] < dist_matrix[b][c] and dist_matrix[a][b] < dist_matrix[b][d]
    else:
        return dist_matrix[a][b] <= dist_matrix[a][c] and dist_matrix[a][b] <= dist_matrix[a][d] and dist_matrix[a][b] <= dist_matrix[b][c] and dist_matrix[a][b] <= dist_matrix[b][d]

def print_matrix(matrix):
    print "\n"
    matrix_line = "    "
    for i in sorted(matrix.iterkeys()):
        matrix_line += str(i) + " "
    print matrix_line
    matrix_line = "  "
    for i in sorted(matrix.iterkeys()):
        matrix_line += "--"
    print matrix_line
    
    for i in sorted(matrix.iterkeys()):
        matrix_line = str(i) + " | "
        for j in sorted(matrix.iterkeys()):
            matrix_line += str(matrix[i][j]) + " "
        print matrix_line
    print "\n"

"""
Return the score matrix
"""
def calcul_score(dist_matrix):
    
    score = {}
    eps = 0.001
    indices = dist_matrix.keys()
    
    for i in indices:
        score[i] = {}
        for j in indices:
            score[i][j] = 0
            
    for i in indices[:-3]:
        for j in indices[indices.index(i)+1:-2]:
            for k in indices[indices.index(j)+1:-1]:
                for l in indices[indices.index(k)+1:]:
                    x = dist_matrix[i][j] + dist_matrix[k][l]
                    y = dist_matrix[i][k] + dist_matrix[j][l]
                    z = dist_matrix[i][l] + dist_matrix[j][k]
                    n = 0
                    mi = x
                    
                    if (y < mi):
                        mi = y;
                    if (z < mi):
                        mi = z;
                    
                    e = mi * eps;
                    
                    if x < mi + e:
                        score[i][j] = score[i][j] + 1;
                        score[k][l] = score[k][l] + 1;
                        n = n + 1;
                    
                    if y < mi + e:
                        score[i][k] = score[i][k] + 1;
                        score[j][l] = score[j][l] + 1;
                        n = n + 1;
                        
                    if z < mi + e:
                        score[i][l] = score[i][l] + 1;
                        score[j][k] = score[j][k] + 1;
                        n = n + 1;
                    
                    if n == 3:
                        score[j][i] = score[j][i] + 1;
                        score[l][k] = score[l][k] + 1;
                        score[k][i] = score[k][i] + 1;
                        score[l][j] = score[l][j] + 1;
                        score[l][i] = score[l][i] + 1;
                        score[k][j] = score[k][j] + 1;
    
    print_matrix(score)
    
    return score

"""
return distance moyenne de OI, par rapport a J
"""
def distance_moyenne(i, j, dist_matrix):
        x = 0
        zz = 0
        for m in dist_matrix.keys():
            if (m != i) and (m != j):
                x = x + dist_matrix[i][j] + dist_matrix[i][m] - dist_matrix[j][m]
                zz = zz + 1
                        
        x = x / (zz * 2.0)
        
        if x < 0:
            return 0
        else:
            return x;

"""
Return the tree (tree = "codage pere/fils")
"""
def calcul_arbre(dist_matrix):
    
    tree = {}
    
    #cpt = 0
    score_max = 0
    seuil = 0
    
    while len(dist_matrix.keys()) > 3: # and cpt < 50:
        #cpt += 1
        #print "cpt = " + str(cpt)
        #print "\n*** ITER ***\n"
        print_matrix(dist_matrix)
        score = calcul_score(dist_matrix)
    
        scores = []
        for i in score.iterkeys():
            for j in score.iterkeys():
                if score_max < score[i][j]:
                    scores += [score[i][j]]
        scores = list(set(sorted(scores)))
        

        # voir page 155 "Les arbres et les representations des proximites"
        n = len(score.keys())
        score_max_theoriq = (n-2)*(n-3)/2 
        seuil = score_max_theoriq - (n-3)/2 # A verifier!!!! (n-2)*(n-3)/2 - (n-3)*(n-4)/2 = (n-3) 

        pre_groups = {}
        groups = {}
        cpt = 1
        while not groups:
            
            try:
                score_max = scores[len(scores)-cpt]
            except:
                score_max = 0
            print "SCORE MAX = " + str(score_max)
            print "seuil = " + str(seuil)
            
            # PREGROUP IDENTIFICATION
            for i in sorted(score.keys()):
                for j in sorted(score.keys()):
                    if score[i][j] >= seuil:
                        pre_groups[i] = pre_groups.get(i, [i]) + [j]
                    elif i in pre_groups.keys():
                        for k in pre_groups[i]:
                            if score[k][j] >= score_max:
                                pre_groups[i] = pre_groups.get(i, [i]) + [j]
            # PACK PREGROUP
            pregroups_indice = pre_groups.keys()
            pregroup_indice_to_del = []
            for i in pregroups_indice:
                for j in pregroups_indice:
                    if pre_groups[i] != pre_groups[j] and i not in pregroup_indice_to_del:
                        if set(pre_groups[i]).intersection( set(pre_groups[j])):
                            pre_groups[i] = list(set(pre_groups[i] + pre_groups[j]))
                            pregroup_indice_to_del += [j]
            pre_groups_final = {}
            for i in pre_groups.keys():
                if i not in pregroup_indice_to_del:
                    pre_groups_final[i] = pre_groups[i]
            pre_groups = pre_groups_final
            
            # LOG: print pre_group
            print "PRE-GROUPES:"
            for pre_group in pre_groups.itervalues():
                pre_group_str = ""
                for element in pre_group:
                    if element < 10:
                        pre_group_str += "0" + str(element) + " "
                    else:
                        pre_group_str += str(element) + " "
                print pre_group_str
            
            # GROUP IDENTIFICATION
            g = 0
            for pre_group in pre_groups.values():
                indice = 0
                nc = 0
                for i in  pre_group:
                    k = len(pre_group)
                    for j in  pre_group[indice+1:]:
                        nc += score[j][i] # somme des scores strcts
                    indice += 1
        
                #print "il = " + str(n)
                #print "i2 = " + str(k)
                
                nn = ((n - 2) * (n - 3) / 2 - (n - k) * (n - k - 1) / 2);
                nn = 3 * nn - len(pre_groups) + 1; # Bricolage a la Xuan ?
                if k == 2:
                    nn = 1
        
                #print "nn = " + str(nn)
                #print "nc = " + str(nc)
                
                if  (k == 2 and nc <= nn) or (k != 2 and nc > nn) or (score_max == 0 and seuil == 0):
                    if score_max == 0 and seuil == 0:
                        print "!!!! score max == seuil == 0 !!!!!"
                    groups[g] = pre_group
                    g += 1
            
            # LOG: print pre_group TODO: CALCUL GROUP!!!
            print "GROUPES:"
            for group in groups.itervalues():
                group_str = ""
                for element in group:
                    if element < 10:
                        group_str += "0" + str(element) + " "
                    else:
                        group_str += str(element) + " "
                    
                print group_str
            
            # Si pas de groupe on prend un groupe avec le score MAX en dessous du seuil
            if not groups:
                print "Seuil non atteint..."
                if seuil != score_max:
                    seuil = score_max
                else:
                    cpt += 1
                pre_groups = {}
        
        # fin while
                    
        seuil = 0
        score_max = 0
        
        # REPLACE GROUP : ALGORITHM 1
        new_point = len(dist_matrix.keys())
        for element in tree.values():
            new_point += len(element.keys())
        
        new_points = []
        to_delete = []
        for group in groups.itervalues():
            tree[new_point] = {}
            indice = 0
            for i in group:
                new_points += [new_point]
                to_delete += [i]
                for j in group[indice+1:]:
                    to_delete += [j]
                    tree[new_point][i] = distance_moyenne(i, j, dist_matrix)
                    print str(new_point) + " ***Pere: " + str(i) + " ***Fils: " + str(tree[new_point][i])
                    tree[new_point][j] = distance_moyenne(j, i, dist_matrix)
                    print str(new_point) + " ***Pere: " + str(j) + " ***Fils: " + str(tree[new_point][j])
                indice += 1
            new_point += 1

        new_points = list(set(new_points))
        to_delete = list(set(to_delete))
        
        for i in new_points:
            list_j = dist_matrix.keys()
            dist_matrix_cp = dist_matrix.copy()
            for j in list_j:
                distance = distance_moyenne(tree[i].keys()[0], j, dist_matrix_cp)
                dist_matrix[i] =  dist_matrix.get(i, {})
                dist_matrix[j] =  dist_matrix.get(j, {})
                dist_matrix[i][j] = distance
                dist_matrix[i][i] = 0
                dist_matrix[j][j] = 0
                dist_matrix[j][i] = distance
        
        for i in to_delete:
            del dist_matrix[i]
        
    # end while
    
    indice_max = 0
    for i in dist_matrix.keys():
        if i > indice_max:
            indice_max = i
    for i in dist_matrix.keys():
        if i != indice_max:
            tree[indice_max][i] = dist_matrix[indice_max][i]
    
    return calcul_coord(tree)
            
"""
Test the algorithm
"""
def main():
    
    if len(sys.argv) < 2:
        print 'Err: bad parameters!'
        return 1
    
    f = open(sys.argv[1])

    # CREATE THE INPUT MATRIX    
    matrix = {}
    i = 0
    for line in f:
        matrix[i] = {}
        j = 0
        for value in line.split():
            matrix[i][j] = float(value)
            j += 1
        i += 1
    f.close()
    
    # detect if it's square
    is_square = True
    for i in matrix.keys():
        for j in matrix[i].keys():
            try:
                if matrix[i][j] != matrix[j][i] or matrix[i][i] != 0:
                    is_square = False
                    break;
            except:
                is_square = False
                break;
    print "Matrice de  distances : ", is_square

    if not is_square:
        matrix_tmp = {}
        for line, parts in matrix.iteritems():
            for column, value in parts.iteritems():
                matrix_tmp[column] = matrix_tmp.get(column, {})
                matrix_tmp[column][line] = value
        matrix = matrix_tmp
        print matrix
        matrix = calcul_khi2(matrix)
    print_matrix(matrix)
    
    nodes, root_id = calcul_arbre(matrix)
    #print_tree(nodes[root_id])
    
    data_json = {}
    data_json["nodes"] = []
    data_json["links"] = []
    
    for node in nodes.values():
        n = {}
        n["x"] = node.x
        n["y"] = node.y
        n["label"] = node.id
        data_json["nodes"] += [n]
        
        for son in node.fils.values():
            l = {}
            l["source"] = node.id
            l["target"] = son.id
            data_json["links"] += [l]
            
    f = open(sys.argv[1].replace("txt", "json"), "w")
    f.write(json.dumps(data_json))
    f.close()
    
if __name__ == '__main__':
    main()
    
    
