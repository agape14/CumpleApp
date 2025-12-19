/**
 * EJEMPLO DE CONFIGURACIÓN API PARA REACT NATIVE
 * 
 * Este archivo muestra cómo configurar la comunicación con tu API Laravel
 * desde React Native.
 * 
 * Ubicación: src/api/api.js
 */

import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// ⚠️ IMPORTANTE: Cambiar según tu entorno
// Para emulador Android: http://10.0.2.2:8000
// Para simulador iOS: http://localhost:8000
// Para dispositivo físico: http://TU_IP_LOCAL:8000 (ej: http://192.168.1.100:8000)
const API_BASE_URL = __DEV__ 
  ? 'http://10.0.2.2:8000/api/v1'  // Desarrollo Android
  : 'https://tu-dominio.com/api/v1'; // Producción

// Crear instancia de axios
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor para agregar token de autenticación
api.interceptors.request.use(
  async (config) => {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    } catch (error) {
      console.error('Error obteniendo token:', error);
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor para manejar errores de respuesta
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Token expirado o inválido
      await AsyncStorage.removeItem('auth_token');
      await AsyncStorage.removeItem('user_data');
      // Aquí podrías redirigir al login
    }
    return Promise.reject(error);
  }
);

export default api;

/**
 * EJEMPLO DE SERVICIO DE AUTENTICACIÓN
 * Ubicación: src/api/auth.js
 */

export const authService = {
  /**
   * Inicia sesión con DNI y contraseña
   */
  login: async (dni, password) => {
    try {
      const response = await api.post('/login', {
        dni,
        password,
      });
      
      if (response.data.success) {
        // Guardar token y datos del usuario
        await AsyncStorage.setItem('auth_token', response.data.token);
        await AsyncStorage.setItem('user_data', JSON.stringify(response.data.user));
      }
      
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error de conexión' };
    }
  },

  /**
   * Cierra sesión
   */
  logout: async () => {
    try {
      await api.post('/logout');
      await AsyncStorage.removeItem('auth_token');
      await AsyncStorage.removeItem('user_data');
      return { success: true };
    } catch (error) {
      // Aunque falle, limpiar el storage local
      await AsyncStorage.removeItem('auth_token');
      await AsyncStorage.removeItem('user_data');
      return { success: true };
    }
  },

  /**
   * Verifica si el usuario está autenticado
   */
  isAuthenticated: async () => {
    const token = await AsyncStorage.getItem('auth_token');
    return !!token;
  },

  /**
   * Obtiene el token guardado
   */
  getToken: async () => {
    return await AsyncStorage.getItem('auth_token');
  },
};

/**
 * EJEMPLO DE SERVICIO DE FAMILIARES
 * Ubicación: src/api/familiares.js
 */

export const familiaresService = {
  /**
   * Obtiene todos los familiares
   */
  getAll: async (filters = {}) => {
    try {
      const response = await api.get('/familiares', { params: filters });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error al obtener familiares' };
    }
  },

  /**
   * Obtiene un familiar por ID
   */
  getById: async (id) => {
    try {
      const response = await api.get(`/familiares/${id}`);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error al obtener familiar' };
    }
  },

  /**
   * Crea un nuevo familiar
   */
  create: async (data) => {
    try {
      const response = await api.post('/familiares', data);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error al crear familiar' };
    }
  },

  /**
   * Actualiza un familiar
   */
  update: async (id, data) => {
    try {
      const response = await api.put(`/familiares/${id}`, data);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error al actualizar familiar' };
    }
  },

  /**
   * Elimina un familiar
   */
  delete: async (id) => {
    try {
      const response = await api.delete(`/familiares/${id}`);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error al eliminar familiar' };
    }
  },

  /**
   * Obtiene próximos cumpleaños
   */
  getProximosCumpleanos: async (dias = 30) => {
    try {
      const response = await api.get('/familiares/proximos-cumpleanos', {
        params: { dias },
      });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error al obtener próximos cumpleaños' };
    }
  },

  /**
   * Obtiene estadísticas del dashboard
   */
  getDashboard: async () => {
    try {
      const response = await api.get('/familiares/dashboard');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Error al obtener estadísticas' };
    }
  },
};

/**
 * EJEMPLO DE USO EN UN COMPONENTE
 * Ubicación: src/screens/FamiliaresScreen.js
 */

/*
import React, { useState, useEffect } from 'react';
import { View, FlatList, ActivityIndicator, Alert } from 'react-native';
import { familiaresService } from '../api/familiares';
import FamiliarCard from '../components/FamiliarCard';

const FamiliaresScreen = () => {
  const [familiares, setFamiliares] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadFamiliares();
  }, []);

  const loadFamiliares = async () => {
    try {
      setLoading(true);
      const response = await familiaresService.getAll();
      if (response.success) {
        setFamiliares(response.data);
      }
    } catch (error) {
      Alert.alert('Error', error.message || 'No se pudieron cargar los familiares');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    loadFamiliares();
  };

  if (loading && !refreshing) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return (
    <FlatList
      data={familiares}
      keyExtractor={(item) => item.id.toString()}
      renderItem={({ item }) => <FamiliarCard familiar={item} />}
      refreshing={refreshing}
      onRefresh={handleRefresh}
      ListEmptyComponent={
        <View style={{ padding: 20, alignItems: 'center' }}>
          <Text>No hay familiares registrados</Text>
        </View>
      }
    />
  );
};

export default FamiliaresScreen;
*/

