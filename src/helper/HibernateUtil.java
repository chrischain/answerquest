package helper;

import java.util.List;

import javax.persistence.EntityManager;
import javax.persistence.EntityManagerFactory;
import javax.persistence.Persistence;
import javax.persistence.Query;

public class HibernateUtil {

	private static EntityManagerFactory entityManagerFactory = null;

	public static EntityManagerFactory getEntityManagerFactory() {
		
		
		if (entityManagerFactory == null) {
			try {
				entityManagerFactory = Persistence.createEntityManagerFactory("aq.jpa");
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		return entityManagerFactory;
	}
	
	@SuppressWarnings("unchecked")
	public static <T> List<T> getResults(String ejbql, Object[] parameters) {
		List<T> results = null;
		try {
			EntityManager entityManager = getEntityManagerFactory().createEntityManager();
			Query query = entityManager.createQuery(ejbql);
			if (parameters != null) {
				for(int idx = 0, size = parameters.length; idx < size; idx++) {
					query.setParameter(idx + 1, parameters[idx]);
				}
			}
			results = (List<T>) query.getResultList();
			entityManager.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return results;
	}
	
	public static long getCount(String ejbql, Object[] parameters) {
		long count = -1;
		try {
			EntityManager entityManager = getEntityManagerFactory().createEntityManager();
			Query query = entityManager.createQuery(ejbql);
			if (parameters != null) {
				for(int idx = 0, size = parameters.length; idx < size; idx++) {
					query.setParameter(idx + 1, parameters[idx]);
				}
			}
			count = (Long) query.getSingleResult();
			entityManager.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
		
		return count;
	}
	
	@SuppressWarnings("unchecked")
	public static <T> List<T> getResults(String ejbql, Object[] parameters, int firstResult, int pageSize) {
		List<T> results = null;
		try {
			EntityManager entityManager = getEntityManagerFactory().createEntityManager();
			Query query = entityManager.createQuery(ejbql);
			query.setFirstResult(firstResult);
			query.setMaxResults(pageSize);
			if (parameters != null) {
				for(int idx = 0, size = parameters.length; idx < size; idx++) {
					query.setParameter(idx + 1, parameters[idx]);
				}
			}
			results = (List<T>) query.getResultList();
			entityManager.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return results;
	}
	
	public static int execute(String ejbql, Object[] parameters) {
		EntityManager entityManager = getEntityManagerFactory().createEntityManager();
		Query query = entityManager.createQuery(ejbql);
		if (parameters != null) {
			for (int idx = 0, size = parameters.length; idx < size; idx++) {
				query.setParameter(idx + 1, parameters[idx]);
			}
		}
		entityManager.getTransaction().begin();
		int result = query.executeUpdate();
		entityManager.getTransaction().commit();
		return result;
	}

	public static Long getRowCount(String rowCountQuery) {
		Long result = null;
		try {
			EntityManager entityManager = getEntityManagerFactory().createEntityManager();
			Query query = entityManager.createQuery(rowCountQuery);
			result = (Long) query.getSingleResult();
			entityManager.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return result;
	}
}
